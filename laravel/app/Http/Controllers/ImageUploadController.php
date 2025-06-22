<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ImageUploadController extends Controller
{
    public function store(Request $request)
    {
        Log::info('--- 画像アップロード開始 ---');

        // リクエスト内容の確認
        Log::info('リクエストデータ', $request->all());
        Log::info('ファイルが存在するか？', ['hasFile' => $request->hasFile('file')]);

        if (!$request->hasFile('file')) {
            Log::error('ファイルが見つかりません');
            return response()->json(['error' => 'ファイルが見つかりません'], 400);
        }

        $file = $request->file('file');

        Log::info('アップロードされたファイル名', ['originalName' => $file->getClientOriginalName()]);
        Log::info('MIMEタイプ', ['mimeType' => $file->getMimeType()]);
        Log::info('ファイルサイズ', ['size' => $file->getSize()]);
        Log::info('一時ファイルパス', ['path' => $file->getPathname()]);
        Log::info('isValid()', ['valid' => $file->isValid()]);

        if (!$file->isValid()) {
            Log::error('無効なファイルです');
            return response()->json(['error' => '無効なファイルです'], 400);
        }

        $original = $file->getClientOriginalName();
        $extension = $file->getClientOriginalExtension();
        $filename = time() . '_' . uniqid() . '.' . $extension;

        $uploadPath = public_path('uploads');

        Log::info('保存先ディレクトリ', ['path' => $uploadPath]);
        Log::info('生成されたファイル名', ['filename' => $filename]);

        // ディレクトリがなければ作成
        if (!file_exists($uploadPath)) {
            mkdir($uploadPath, 0775, true);
            Log::info('uploads フォルダを作成しました');
        }

        $file->move($uploadPath, $filename);

        $fullPath = $uploadPath . '/' . $filename;
        $url = url('uploads/' . $filename);

        Log::info('保存されたパス', ['fullPath' => $fullPath]);
        Log::info('アクセスURL', ['url' => $url]);
        Log::info('ファイル存在チェック', ['exists' => file_exists($fullPath)]);

        Log::info('--- 画像アップロード完了 ---');

        return response()->json(['location' => $url]);
    }
}
