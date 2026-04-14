<?php

namespace App\Http\Controllers\API;

use App\Ai\Agents\CvBuilder;
use App\Models\Cv;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CvAiController extends BaseController
{
    /**
     * Chat with the CV builder assistant about a specific CV.
     *
     * On the first message (no conversation_id), the CV data is injected as context.
     * On follow-up messages, pass the conversation_id to continue the conversation.
     */
    public function chat(Request $request, $cv_id): JsonResponse
    {
        $request->validate([
            'message' => 'required|string|max:2000',
            'conversation_id' => 'nullable|string',
        ]);

        $cv = auth()->user()->cvs()->find($cv_id);

        if (is_null($cv)) {
            return $this->sendError([], 'CV not found', 404);
        }

        $agent = CvBuilder::make();

        if ($request->conversation_id) {
            $agent->continue($request->conversation_id, auth()->user());
            $prompt = $request->message;
        } else {
            $agent->forUser(auth()->user());
            $prompt = $this->buildInitialPrompt($cv, $request->message);
        }

        $response = $agent->prompt($prompt);

        return $this->sendResponse([
            'message' => $response->text(),
            'conversation_id' => $response->conversationId,
        ], 'Response generated successfully');
    }

    /**
     * Prepend CV data to the first user message so the agent has full context.
     */
    private function buildInitialPrompt(Cv $cv, string $userMessage): string
    {
        $cvData = $this->formatCv($cv);

        return "Here is my CV:\n\n{$cvData}\n\n{$userMessage}";
    }

    /**
     * Format a CV model into a readable text block for the AI prompt.
     */
    private function formatCv(Cv $cv): string
    {
        $parts = [];

        if ($cv->name) {
            $parts[] = "Name: {$cv->name}";
        }
        if ($cv->email) {
            $parts[] = "Email: {$cv->email}";
        }
        if ($cv->phone) {
            $parts[] = "Phone: {$cv->phone}";
        }
        if ($cv->location) {
            $parts[] = "Location: {$cv->location}";
        }
        if ($cv->links) {
            $parts[] = "Links:\n{$cv->links}";
        }
        if ($cv->bio) {
            $parts[] = "Professional Summary:\n{$cv->bio}";
        }
        if ($cv->experience) {
            $parts[] = "Experience:\n{$cv->experience}";
        }
        if ($cv->education) {
            $parts[] = "Education:\n{$cv->education}";
        }
        if ($cv->skills) {
            $parts[] = "Skills:\n{$cv->skills}";
        }

        return implode("\n\n", $parts);
    }
}
