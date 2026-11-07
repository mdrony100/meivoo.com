<?php

namespace Database\Seeders;

use App\Models\Conversation;
use App\Models\ConversationParticipant;
use App\Models\Family;
use App\Models\FamilyMember;
use App\Models\Message;
use App\Models\User;
use Illuminate\Database\Seeder;

class DemoMessagingSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::orderBy('id')->take(3)->get();
        if ($users->count() < 3) {
            return;
        }

        $family = Family::where('slug', 'rahman-family')->first();
        [$a, $b, $c] = [$users[0], $users[1], $users[2]];

        $threads = [
            [$b, [['from' => 'them', 'body' => 'Are we still on for the reunion planning call tonight?']],
                 [['from' => 'me', 'body' => 'Yes — I will send the venue shortlist before 8.']],
                 [['from' => 'them', 'body' => 'Perfect. Ammu wants to add the old photo albums too.']]],
            [$c, [['from' => 'them', 'body' => 'Found grandpa\'s letters in the almirah. Should I scan them?']],
                 [['from' => 'me', 'body' => 'Please do. They can go straight into the legacy names section.']]],
        ];

        foreach ($threads as $index => [$other, $exchanges]) {
            $conversation = Conversation::create([
                'conversation_type' => 'direct',
                'created_by_user_id' => $a->id,
                'last_message_at' => now()->subMinutes(12 * ($index + 1)),
            ]);

            foreach ([$a, $other] as $participant) {
                ConversationParticipant::create([
                    'conversation_id' => $conversation->id,
                    'user_id' => $participant->id,
                    'role' => 'member',
                    'last_read_at' => $participant->id === $a->id ? now()->subDay() : now(),
                ]);
            }

            foreach ($exchanges as $offset => $exchange) {
                $fromMe = $exchange['from'] === 'me';
                Message::create([
                    'conversation_id' => $conversation->id,
                    'sender_user_id' => $fromMe ? $a->id : $other->id,
                    'from_user_id' => $fromMe ? $a->id : $other->id,
                    'to_user_id' => $fromMe ? $other->id : $a->id,
                    'is_read' => 1,
                    'message_type' => 'text',
                    'body' => $exchange['body'],
                    'created_at' => now()->subMinutes(30 - $offset * 5),
                    'updated_at' => now()->subMinutes(30 - $offset * 5),
                ]);
            }
        }

        // direct (from/to) messages, so the notification badge has real unread items
        foreach ([[$b, 'Sent you the venue photos'], [$c, 'The tree update is live — have a look']] as $i => [$sender, $body]) {
            Message::create([
                'family_id' => $family?->id,
                'from_user_id' => $sender->id,
                'to_user_id' => $a->id,
                'message' => $body,
                'is_read' => 0,
                'created_at' => now()->subMinutes(20 + $i * 7),
                'updated_at' => now()->subMinutes(20 + $i * 7),
            ]);
        }

        FamilyMember::whereNull('user_id')->limit(0)->get();
    }
}
