<?php 

namespace App\Ai;

use Illuminate\Support\Facades\Http;
use OpenAI\Laravel\Facades\OpenAI;

class Chat 
{

    protected array $messages = [];

    public function systemMessage(string $message)
    {
        $this->messages[] = [
            "role" => "system",
            "content" => $message
        ];
        return $this;
    }

    public function send(string $message, bool $speech = false): ?string
    {
        $this->messages[] = [
            "role" => "user",
            "content" => $message
        ];

       /*  $response = Http::withToken(config('services.openai.secret'))
            ->post('https://api.openai.com/v1/chat/completions', 
                [
                    "model" => "gpt-3.5-turbo",
                    "messages"=>  $this->messages
                
                ])->json('choices.0.message.content'); */

        $response = OpenAI::chat()->create([
            "model" => "gpt-3.5-turbo",
            "messages"=>  $this->messages
        ])->choices[0]->message->content;
        

        
        
        if($response) {
                $this->messages[] = [
                    "role" => "assistant",
                    "content" => $response
                ];   

            }
          
        return $speech ? $this->speech($response) : $response;


    }

    public function speech(string $message): string
    {
       // dd('speech',$message);
        return OpenAI::audio()->speech([
            "model" => 'tts-1',
            'input' => $message,
            'voice' => 'alloy',
        ]);      
    }    

    public function reply(string $message): ?string
    {
       return $this->send($message);
    }

    public function messages()
    {
      return $this->messages;
    }



}
