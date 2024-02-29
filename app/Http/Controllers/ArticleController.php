<?php

namespace App\Http\Controllers;

use ZipArchive;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Article;
use App\Models\File;
use App\Models\User;


class ArticleController extends Controller
{



    public function showUploadForm()
    {
        return view('upload-form');
    }

    // public function upload(Request $request)
    // {
    //     // Validate the request
    //     $request->validate([
    //         'title' => 'required|string|max:255',
    //         'article' => 'required|mimes:doc,docx',
    //         'images.*' => 'image|mimes:jpg,jpeg,png|max:2048' // Adjust max size as needed
    //     ]);

    //     // Store article details
    //     // $article = auth()->user()->articles()->create([
    //     //     'title' => $request->title
    //     // ]);
    //     // Assuming you have a user with ID 1

    //     $user = User::find(1);

    //     $article = $user->articles()->create([
    //         'title' => $request->title
    //     ]);



    //     // Store article file
    //     $articleFilePath = $request->file('article')->store('articles/' . auth()->id());

    //     // Store article images
    //     if ($request->hasFile('images')) {
    //         foreach ($request->file('images') as $image) {
    //             $imagePath = $image->store('articles/' . auth()->id());
    //             $article->files()->create(['file_path' => $imagePath]);
    //         }
    //     }

    //     return redirect()->route('upload.success');
    // }


    protected $fillable = ['title', 'user_id', 'file_path'];

    // public function upload(Request $request)
    // {
    //     $request->validate([
    //         'title' => 'required|string|max:255',
    //         'article' => 'required|mimes:doc,docx',
    //         'images.*' => 'image|mimes:jpg,jpeg,png|max:2048' // Adjust the size as needed
    //     ]);

    //     // Assuming you're manually managing the user authentication for now
    //     // $userId = 1; // Or however you determine the user's ID


    //     $user = User::find(1);

    //     // $article = $user->articles()->create([
    //     //     'title' => $request->title
    //     // ]);


    //     // Create a unique directory for this specific article upload
    //     $uniqueFolder = 'user' . $user->id . '_' . now()->format('YmdHis');
    //     $articleDirectory = 'public/articles/' . $uniqueFolder;

    //     Storage::makeDirectory($articleDirectory);

    //     // Store article details - adjust this part according to your actual user management

    //     $article = $user->articles()->create([
    //         'user_id' => 1, // Assuming '1' is an existing user ID in your database
    //         'title' => $request->title,
    //         // Include any other fields as necessary
    //     ]);


    //     // Store the article's Word document
    //     $articleFilePath = $request->file('article')->store($articleDirectory);

    //     // Update the article with the file path
    //     $article->update(['file_path' => $articleFilePath]);

    //     // Store article images
    //     if ($request->hasFile('images')) {
    //         foreach ($request->file('images') as $image) {
    //             $imagePath = $image->store($articleDirectory);
    //             // Assuming you have a method to associate files with the article
    //             $article->files()->create(['file_path' => $imagePath]);
    //         }
    //     }

    //     return redirect()->route('upload.success'); // Ensure you have this route defined
    // }


    // public function upload(Request $request)
    // {
    //     $request->validate([
    //         'title' => 'required|string|max:255',
    //         'article' => 'required|mimes:doc,docx',
    //         'images.*' => 'image|mimes:jpg,jpeg,png|max:2048' // Adjust max size as needed
    //     ]);

    //     // Create a unique directory for the user's articles
    //     $userArticleDirectory = 'articles/' . auth()->id();
    //     Storage::makeDirectory($userArticleDirectory);


    //     $user = User::find(1);

    //     $article = $user->articles()->create([
    //         'title' => $request->title
    //     ]);

    //     // Store article file
    //     $articleFilePath = $request->file('article')->store($userArticleDirectory . '/words');

    //     // Store article images
    //     if ($request->hasFile('images')) {
    //         foreach ($request->file('images') as $image) {
    //             $imagePath = $image->store($userArticleDirectory . '/images');
    //             $article->files()->create(['file_path' => $imagePath]);
    //         }
    //     }

    //     return redirect()->route('upload.success');
    // }

    public function downloadArticlesZip()
    {
        $zip = new ZipArchive;
        $zipFileName = 'articles_' . now()->format('YmdHis') . '.zip';
        $zipPath = storage_path('app/public/' . $zipFileName);

        if ($zip->open($zipPath, ZipArchive::CREATE) === TRUE) {
            // Retrieve all articles with status 1
            $articles = Article::where('status', 1)->get();

            foreach ($articles as $article) {
                $articleFolderPath = storage_path('app/public/articles/user' . $article->user_id . '_' . $article->created_at->format('YmdHis'));

                $files = Storage::files('public/articles/user' . $article->user_id . '_' . $article->created_at->format('YmdHis'));

                foreach ($files as $file) {
                    // Add files to zip
                    $relativePath = substr($file, strlen('public/'));
                    $zip->addFile(storage_path('app/' . $file), $relativePath);
                }
            }

            $zip->close();

            // Download ZIP
            return response()->download($zipPath)->deleteFileAfterSend(true);
        } else {
            return redirect()->back()->with('error', 'Cannot create ZIP file.');
        }
    }


    // public function downloadArticlesZip()
    // {
    //     $zip = new ZipArchive;
    //     $zipFileName = 'articles_' . now()->format('YmdHis') . '.zip';
    //     $zipPath = storage_path('app/public/' . $zipFileName);

    //     if ($zip->open($zipPath, ZipArchive::CREATE) === TRUE) {
    //         // Retrieve all articles or filter as needed
    //         $articles = Article::all();

    //         foreach ($articles as $article) {
    //             $articleFolderPath = storage_path('app/public/articles/user' . $article->user_id . '_' . $article->created_at->format('YmdHis'));

    //             $files = Storage::files('public/articles/user' . $article->user_id . '_' . $article->created_at->format('YmdHis'));

    //             foreach ($files as $file) {
    //                 // Add files to zip
    //                 $relativePath = substr($file, strlen('public/'));
    //                 $zip->addFile(storage_path('app/' . $file), $relativePath);
    //             }
    //         }

    //         $zip->close();

    //         // Download ZIP
    //         return response()->download($zipPath)->deleteFileAfterSend(true);
    //     } else {
    //         return redirect()->back()->with('error', 'Cannot create ZIP file.');
    //     };
    // }



    public function upload(Request $request)
    {

        $request->validate([
            'title' => 'required|string|max:255',
            'article' => 'required|mimes:doc,docx',
            'images.*' => 'image|mimes:jpg,jpeg,png|max:2048' // Adjust the size as needed
        ]);

        // Assuming you're manually managing the user authentication for now
        // $userId = 1; // Or however you determine the user's ID
        $user = User::find(2);

        // Create a unique directory for this specific article upload
        $uniqueFolder = 'user' . $user->id . '_' . now()->format('YmdHis');
        $articleDirectory = 'public/articles/' . $uniqueFolder;

        Storage::makeDirectory($articleDirectory);


        $article = $user->articles()->create([
            'user_id' => $user->id,
            'title' => $request->title,
            // Include other fields as necessary
        ]);

        // Store the article's Word document
        $articleFilePath = $request->file('article')->store($articleDirectory);

        // Update the article with the file path
        $article->update(['file_path' => $articleFilePath]);

        // Store article images
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $imagePath = $image->store($articleDirectory);
                // Assuming you have a method to associate files with the article
                $article->files()->create(['file_path' => $imagePath]);
            }
        }

        return redirect()->route('upload.success'); // Ensure you have this route defined
    }





    public function downloadZip()
    {
        // Get all user articles
        $articles = Article::with('files')->get();

        // Create a temporary directory to store article files
        $tempDirectory = storage_path('app/temp');
        if (!file_exists($tempDirectory)) {
            mkdir($tempDirectory);
        }

        // Loop through articles, copy files to temporary directory
        foreach ($articles as $article) {
            foreach ($article->files as $file) {
                Storage::copy($file->file_path, $tempDirectory . '/' . basename($file->file_path));
            }
        }

        // Create ZIP file
        $zipFileName = 'user_articles_' . now()->format('YmdHis') . '.zip';
        $zipFilePath = storage_path('app/public/' . $zipFileName);

        $zip = new ZipArchive;
        if ($zip->open($zipFilePath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === true) {
            $files = glob($tempDirectory . '/*');
            foreach ($files as $file) {
                $zip->addFile($file, basename($file));
            }
            $zip->close();
        }

        // Delete temporary directory
        $this->deleteDirectory($tempDirectory);

        // Download ZIP file
        return response()->download($zipFilePath)->deleteFileAfterSend(true);
    }

    private function deleteDirectory($directory)
    {
        if (!file_exists($directory)) {
            return;
        }

        $files = array_diff(scandir($directory), ['.', '..']);
        foreach ($files as $file) {
            (is_dir("$directory/$file")) ? $this->deleteDirectory("$directory/$file") : unlink("$directory/$file");
        }

        rmdir($directory);
    }
}
