<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'file_name',
        'file_path',
        'type'
    ];

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    /**
     * Загружает файл в папку "documents" и сохраняет путь и имя файла в базе.
     *
     * @param \Illuminate\Http\UploadedFile $file
     * @return Document
     */
    public function upload($file, $student_id)
    {
        $path = $file->store('documents');

        // Создаем новую запись в БД
        return self::create([
            'student_id' => $student_id,
            'file_name' => $file->getClientOriginalName(),
            'file_path' => $path,
        ]);
    }

}
