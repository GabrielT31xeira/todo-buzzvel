<?php

namespace App\Http\Controllers;

use App\Models\Files;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class TaskController extends Controller
{
    public function index()
    {
        $tasks = Task::with('creator', 'updater', 'files')->get();
        return response()->json(['message' => 'Task list', 'tasks' => $tasks], 200);
    }

    public function store(Request $req)
    {
        $validate = Validator::make($req->all(),[
            'title' => 'required|unique:task',
            'description' => 'required',
            'pdf.*' => ['max:20000']
        ]);

        if ($validate->fails()) {
            return response()->json(['error' => $validate->errors()], 422);
        }

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

            return response()->json(['message' => 'task created'], 201);
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
        $validate = Validator::make($req->all(),[
            'title' => 'required|unique',
            'description' => 'required',
            'pdf' => 'required'
        ]);

        if ($validate->fails()) {
            return response()->json(['error' => $validate->errors()], 422);
        }

        DB::beginTransaction();
        try {
            $task = Task::with('creator', 'updater', 'files')->find($id);
            if (!$task) {
                return response()->json(['message' => 'task not found'], 404);
            }

            $user = Auth::user();
            $task->title = $req->input('title');
            $task->description = $req->input('description');
            if ($req->input('completed'))
            {
                $task->completed = $req->input('completed');
                $data = new DateTime();
                $task->completed_at = $data->format('Y-m-d');
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
