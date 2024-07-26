<?php

namespace App\Console\Commands;

use App\AI\Assistant;
use App\Ai\Chat;
use Illuminate\Console\Command;
use function Laravel\Prompts\{outro, text, info, spin};

class ChatCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'chat {--system=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Start a chat with OpenAI';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        /* $chat = new Chat();

        if($this->option('system')) {
            $chat->systemMessage($this->option('system'));
        }

        $question = text(
            label: 'hati is your question for Ai?',
            required:true

        );

        $response = spin(fn() => $chat->send($question), 'Sending request...');

        info($response);

        while ($question = text('Do you want to respond?')) {
            info(
                spin(fn() => $chat->send($question), 'Sending request...')
            );
        }

        info('Conversation over.');
        */
        
        $chat = new Chat();

        if($this->option('system')) {
            $chat->systemMessage($this->option('system'));
        }

        $question = text(
            label: 'hati is your question for Ai?',
            required:true

        );

        $response = spin(fn() => $chat->send($question), 'Sending request...');
        
        info($response);

        while (text('Do you want to respond?')) {

            $question = text('What is your reply?');
            $response = spin(fn() => $chat->send($question), 'Sending request...');
            info($response);
            
        }

        info('Conversation over.');
    
    }        
}   
 