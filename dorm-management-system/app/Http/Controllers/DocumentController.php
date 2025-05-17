<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Document;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class DocumentController extends Controller
{
    public function index()
    {
        $documents = Document::where('student_id', auth()->id())->get();
        return view('student.documents', compact('documents'));
    }

    public function store(Request $request)
    {
        try {
            \Log::info('Запрос на загрузку документа', ['user_id' => Auth::id()]);

            $request->validate([
                'documentFile' => 'required|file|mimes:jpg,jpeg,png,pdf,doc,docx',
            ]);

            if (!$request->hasFile('documentFile')) {
                return back()->with('error', 'Файл не выбран!');
            }

            $file = $request->file('documentFile');
            $path = $file->store('documents');

            $document = Document::create([
                'student_id' => Auth::id(),
                'file_name' => $file->getClientOriginalName(),
                'file_path' => $path,
            ]);
            dd($document);

            \Log::info('Документ загружен:', ['document' => $document]);

            if (!$document) {
                throw new \Exception('Не удалось сохранить документ в БД');
            }

            return redirect()->back()->with('success', 'Документ успешно загружен!');

        } catch (\Exception $e) {
            \Log::error('Ошибка при загрузке документа:', ['error' => $e->getMessage()]);
            return back()->with('error', 'Ошибка загрузки: ' . $e->getMessage());
        }
    }
    public function download(Document $document)
    {
        if ($document->student_id !== auth()->id()) {
            abort(403);
        }

        return Storage::disk('public')->download($document->file_path, $document->file_name);
    }
    public function upload(Request $request)
    {
        try {
            $request->validate([
                'documentFile' => 'required|file|mimes:jpg,jpeg,png,pdf,doc,docx',
                'documentType' => 'required|string'
            ]);

            if (!$request->hasFile('documentFile')) {
                return back()->with('error', 'Файл не выбран!');
            }

            $file = $request->file('documentFile');
            $path = $file->store('documents', 'public');

            $document = Document::create([
                'student_id' => Auth::id(),
                'file_name' => $file->getClientOriginalName(),
                'file_path' => $path,
                'type' => $request->documentType
            ]);

            return redirect()->back()->with('success', 'Документ успешно загружен!');

        } catch (\Exception $e) {
            \Log::error('Ошибка при загрузке документа:', ['error' => $e->getMessage()]);
            return back()->with('error', 'Ошибка загрузки: ' . $e->getMessage());
        }
    }
    public function getUserDocuments($userId)
    {
        $documents = Document::where('student_id', $userId)->get();
        return response()->json($documents);
    }

    public function downloadForManager(Document $document)
    {
        if (auth()->user()->role !== 'manager') {
            abort(403);
        }

        return Storage::disk('public')->download($document->file_path, $document->file_name);
    }

}
