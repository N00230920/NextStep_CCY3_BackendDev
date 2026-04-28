<?php

namespace App\Ai\Agents;

//Laravel can automatically store and retrieve conversation history for the agent, 
// useing the RemembersConversations trait.
use Laravel\Ai\Concerns\RemembersConversations;
use Laravel\Ai\Contracts\Agent;
use Laravel\Ai\Contracts\Conversational;
use Laravel\Ai\Promptable;
use Stringable;

class CvBuilder implements Agent, Conversational
{
    use Promptable, RemembersConversations;

    /**
     * Get the instructions that the agent should follow.
     */
    public function instructions(): Stringable|string
    {
        return 'You are a professional CV/resume assistant for NextStep, a job application tracking platform.
Your role is to help users improve their CVs by:
- Analyzing the structure, content, and presentation
- Providing specific, actionable feedback
- Suggesting improvements to make the CV more compelling to employers
- Helping tailor the CV for specific job roles when asked
- Offering advice on formatting, keywords, and professional language

Be encouraging but honest. Provide concrete, practical suggestions rather than vague advice.
When given a CV, point out both strengths and areas for improvement.';
    }
}
