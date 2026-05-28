<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use App\Events\PesanBaruTerkirim; 

class ChatController extends Controller
{
    /**
     * 1. MENGAMBIL DAFTAR KONTAK (TOKO) UNTUK CUSTOMER
     */
    public function getContacts()
    {
        $userId = Auth::id();
        
        if (!$userId) return response()->json(['error' => 'Unauthenticated'], 401);

        $latestMessages = DB::table('messages')
            ->select('chat_id', DB::raw('MAX(id) as last_msg_id'))
            ->groupBy('chat_id');

        $contactsQuery = DB::table('chats')
            ->join('tb_toko', 'chats.toko_id', '=', 'tb_toko.id')
            ->leftJoinSub($latestMessages, 'latest_msg', function ($join) {
                $join->on('chats.id', '=', 'latest_msg.chat_id');
            })
            ->leftJoin('messages', 'messages.id', '=', 'latest_msg.last_msg_id')
            ->where('chats.customer_id', $userId)
            ->select(
                'tb_toko.id as store_id',
                'tb_toko.nama_toko',
                'tb_toko.logo_toko',
                'messages.message_text',
                'messages.message_type',
                'messages.timestamp as last_time',
                DB::raw("(SELECT COUNT(*) FROM messages m2 WHERE m2.chat_id = chats.id AND m2.is_read = 0 AND m2.sender_id != {$userId}) as unread_count")
            )
            ->orderByRaw('CASE WHEN messages.timestamp IS NULL THEN 1 ELSE 0 END, messages.timestamp DESC')
            ->get();

        $contacts = $contactsQuery->map(function ($chat) {
            $previewText = $chat->message_text;
            if ($chat->message_type === 'image') $previewText = '📷 Mengirim Gambar';
            if ($chat->message_type === 'audio') $previewText = '🎤 Voice Note';
            if ($chat->message_type === 'file')  $previewText = '📄 Mengirim Dokumen';
            if (!$previewText) $previewText = 'Belum ada pesan.';

            return [
                'store_id' => $chat->store_id,
                'nama_toko' => $chat->nama_toko,
                'logo_toko' => $chat->logo_toko,
                'last_message' => $previewText,
                'last_time' => $chat->last_time ? $this->formatChatTime($chat->last_time) : '',
                'unread_count' => $chat->unread_count
            ];
        });

        return response()->json($contacts);
    }

    /**
     * 2. MENGAMBIL HISTORI PESAN DENGAN TOKO TERTENTU
     */
    public function getMessages($storeId)
    {
        $userId = Auth::id();
        
        if (!$userId) return response()->json([], 401);

        $chatRoom = DB::table('chats')
            ->where('customer_id', $userId)
            ->where('toko_id', $storeId)
            ->first();

        if (!$chatRoom) {
            return response()->json([]); 
        }

        DB::table('messages')
            ->where('chat_id', $chatRoom->id)
            ->where('sender_id', '!=', $userId)
            ->where('is_read', 0)
            ->update([
                'is_read' => 1,
                'read_at' => Carbon::now()
            ]);

        $messagesQuery = DB::table('messages')
            ->where('chat_id', $chatRoom->id)
            ->orderBy('timestamp', 'asc')
            ->get();

        $messages = $messagesQuery->map(function ($msg) use ($userId) {
            $content = $msg->message_type === 'text' ? $msg->message_text : $msg->file_url;

            return [
                'sender' => ($msg->sender_id == $userId) ? 'user' : 'seller',
                'content' => $content,
                'type' => $msg->message_type,
                'fileName' => $msg->message_type === 'file' ? $msg->message_text : '',
                'is_read' => $msg->is_read, 
                'time' => Carbon::parse($msg->timestamp)->format('H:i')
            ];
        });

        return response()->json($messages);
    }

    /**
     * 3. MENGIRIM PESAN BARU (TEKS, GAMBAR, FILE, VOICE NOTE)
     */
    public function sendMessage(Request $request)
    {
        $userId = Auth::id();
        
        if (!$userId) return response()->json(['status' => 'error', 'message' => 'Unauthenticated'], 401);

        // AMAN UNTUK WEB & MOBILE
        $storeId = $request->input('store_id') ?? $request->input('toko_id');
        $rawMessage = $request->input('message');
        $msgType = $request->input('type', 'text');

        $messageText = $rawMessage;
        $fileUrl = null;

        DB::beginTransaction();
        try {
            $chatRoom = DB::table('chats')
                ->where('customer_id', $userId)
                ->where('toko_id', $storeId)
                ->first();

            if (!$chatRoom) {
                $chatId = DB::table('chats')->insertGetId([
                    'customer_id' => $userId,
                    'toko_id' => $storeId,
                    'status' => 'open',
                    'start_time' => Carbon::now()
                ]);
            } else {
                $chatId = $chatRoom->id;
            }

            if (in_array($msgType, ['image', 'audio', 'file']) && preg_match('/^data:(\w+\/[\w+-.]+);base64,/', $rawMessage, $matches)) {
                $mimeType = $matches[1];
                $extensions = [
                    'image/jpeg' => 'jpg', 'image/png' => 'png', 'image/gif' => 'gif', 'image/webp' => 'webp',
                    'audio/webm' => 'webm', 'audio/mp3' => 'mp3', 'audio/wav' => 'wav', 'audio/ogg' => 'ogg',
                    'application/pdf' => 'pdf', 'application/zip' => 'zip'
                ];

                $extension = $extensions[$mimeType] ?? 'bin';
                $fileData = base64_decode(substr($rawMessage, strpos($rawMessage, ',') + 1));
                $fileName = 'chat_' . time() . '_' . uniqid() . '.' . $extension;
                
                Storage::disk('local')->put('private_chats/' . $fileName, $fileData);
                $fileUrl = route('chat.file', ['filename' => $fileName]);
                $messageText = $msgType === 'file' ? ($request->input('file_name') ?? 'Dokumen') : '';
            } elseif ($msgType === 'text') {
                $fileUrl = null;
            }

            DB::table('messages')->insert([
                'chat_id' => $chatId,
                'sender_id' => $userId,
                'message_text' => $messageText,
                'message_type' => $msgType,
                'file_url' => $fileUrl,
                'is_read' => 0,
                'timestamp' => Carbon::now()
            ]);

            DB::commit();

            $pesanSocket = [
                'content'  => $msgType === 'text' ? $messageText : $fileUrl,
                'sender'   => 'user', 
                'type'     => $msgType,
                'fileName' => $msgType === 'file' ? ($request->input('file_name') ?? 'Dokumen') : '',
                'time'     => date('H:i')
            ];

            try {
                broadcast(new PesanBaruTerkirim($pesanSocket, $storeId))->toOthers();
            } catch (\Exception $e) { }

            return response()->json([
                'status' => 'success',
                'reply' => $msgType === 'text' ? $messageText : $fileUrl,
                'time' => date('H:i')
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    private function formatChatTime($timestamp)
    {
        if (!$timestamp) return '';
        $date = Carbon::parse($timestamp);
        if ($date->isToday()) return $date->format('H:i');
        elseif ($date->isYesterday()) return 'Kemarin';
        else return $date->format('d/m/y');
    }
}