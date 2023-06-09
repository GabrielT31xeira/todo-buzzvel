<?php

namespace App\Http\Controllers;

use App\Models\Files;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TaskController extends Controller
{
    public function index()
    {
        $tasks = Task::with('creator', 'updater', 'files')->get();
        return response()->json(['message' => 'Task list', 'task' => $tasks]);
    }

    public function store(Request $req)
    {
        $req->validate([
            'title' => 'required',
            'description' => 'required',
            'pdf' => 'required'
        ]);

        DB::beginTransaction();
        try {
            $user = Auth::user();
            $task = new Task;
            $task->title = $req->input('title');
            $task->description = $req->input('description');
            $task->completed = false;
            $task->created_by = $user->id;
            $task->save();
            $file = $req->input('pdf');

            foreach ($file as $files){
                $newFile = new Files();
                $newFile->pdf = $files;
                $newFile->task_id = $task->id;
                $newFile->save();
            }
            DB::commit();

            return response()->json(['message' => 'task created'], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function show($id)
    {
        try {
            $task = Task::with('creator', 'updater', 'files')->find($id);
            if (!$task) {
                return response()->json(['message' => 'task not found'], 404);
            }
            return response()->json(['task' => $task],200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function update($id, Request $req)
    {
        $req->validate([
            'title' => 'required',
            'description' => 'required',
            'pdf' => 'required'
        ]);

        DB::beginTransaction();
        try {
            $task = Task::with('creator', 'updater', 'files')->find($id);
            if (!$task) {
                return response()->json(['message' => 'task not found'], 404);
            }

            $user = Auth::user();
            $task->title = $req->input('title');
            $task->description = $req->input('description');
            if ($req->input('completed') != null)
            {
                $task->completed = $req->input('completed');
                $dataAtual = new DateTime();
                $task->completed_date = $dataAtual->format('Y-m-d');
            } else {
                $task->completed = false;
            }
            $task->updated_by = $user->id;
            $task->save();

            $file = $req->input('pdf');

            foreach ($file as $files){
                $newFile = new Files();
                $newFile->pdf = $files;
                $newFile->task_id = $task->id;
                $newFile->save();
            }
            DB::commit();

            return response()->json(['message' => 'Task updated'],200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function delete($id)
    {
        $task = Task::find($id);

        if (!$task) {
            return response()->json(['message' => 'task not found'], 404);
        }

        $task->files()->delete();

        // Exclua a tarefa
        $task->delete();

        return response()->json(['message' => 'task deleted successfully'],200);
    }
}
