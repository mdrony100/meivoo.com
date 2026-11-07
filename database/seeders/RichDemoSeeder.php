<?php

namespace Database\Seeders;

use App\Models\Comment;
use App\Models\Event;
use App\Models\Family;
use App\Models\FamilyMember;
use App\Models\FamilyRelationship;
use App\Models\FeedPost;
use App\Models\LegacyName;
use App\Models\PostLike;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Populates a believable multi-generation family so every screen in the
 * demo has real content: members, relationships, posts, comments, likes,
 * events, legacy names and a message thread.
 */
class RichDemoSeeder extends Seeder
{
    public function run(): void
    {
        $existing = Family::where('slug', 'rahman-family')->first();
        if ($existing) {
            $this->command?->info('Demo family already present — skipping. Existing rows were not deleted.');

            return;
        }

        $users = User::orderBy('id')->take(3)->get();
        $owner = $users->first();

        $family = Family::create([
            'name' => 'The Rahman Family',
            'slug' => 'rahman-family',
            'created_by' => $owner->id,
        ]);

        $today = Carbon::today();
        $birthdayThisMonth = $today->copy()->day(min(12, $today->daysInMonth));
        $anniversaryThisMonth = $today->copy()->day(min(20, $today->daysInMonth));

        // [full_name, gender, birth_date, occupation, generation, status, death_date]
        $people = [
            ['Abdul Karim Rahman', 'male', '1938-04-14', 'Retired school principal', 1, 'passed', '2016-08-02'],
            ['Rahima Begum', 'female', '1944-11-09', 'Homemaker', 1, 'alive', null],
            ['Mizanur Rahman', 'male', '1968-02-21', 'Civil engineer', 2, 'alive', null],
            ['Nasreen Rahman', 'female', $birthdayThisMonth->toDateString(), 'Physician', 2, 'alive', null],
            ['Shafiq Rahman', 'male', '1971-06-30', 'Banker', 2, 'alive', null],
            ['Farhana Akter', 'female', '1975-01-17', 'School teacher', 2, 'alive', null],
            ['Tanvir Rahman', 'male', '1994-09-05', 'Software engineer', 3, 'alive', null],
            ['Sadia Rahman', 'female', '1997-03-28', 'Architect', 3, 'alive', null],
            ['Rafid Rahman', 'male', '2001-12-11', 'University student', 3, 'alive', null],
            ['Nusrat Jahan', 'female', '2003-07-19', 'University student', 3, 'alive', null],
            ['Ayaan Rahman', 'male', '2019-05-23', 'Kindergarten', 4, 'alive', null],
            ['Zara Rahman', 'female', '2022-10-08', 'Playgroup', 4, 'alive', null],
        ];

        $members = [];
        foreach ($people as $i => [$name, $gender, $dob, $job, $generation, $status, $dod]) {
            $user = null;
            if ($i === 2) {
                $user = $users[0] ?? null;         // demo login: Mizanur
            } elseif ($i === 6) {
                $user = $users[1] ?? null;         // second login: Tanvir
            } elseif ($i === 7) {
                $user = $users[2] ?? null;         // third login: Sadia
            }

            $members[] = FamilyMember::create([
                'family_id' => $family->id,
                'user_id' => $user?->id,
                'full_name' => $name,
                'gender' => $gender,
                'birth_date' => $dob,
                'occupation' => $job,
                'generation' => $generation,
                'status' => $status,
                'death_date' => $dod,
            ]);
        }

        $name = fn (int $i) => $members[$i]->id;

        // parent links
        foreach ([[2, 0], [3, 0], [4, 0], [2, 1], [3, 1], [4, 1], [6, 2], [7, 2], [9, 4], [6, 3], [7, 3], [8, 4], [10, 6], [11, 6]] as [$child, $parent]) {
            $members[$child]->update(['parent_id' => $members[$parent]->id]);
        }

        // marriages / partners — stored with both legacy and current column
        // names so either code path reads them; relation_type drives the tree.
        foreach ([[2, 3, '1992-12-05'], [4, 5, '1998-02-14'], [6, 9, '2021-11-26'], [8, 7, '2023-01-15']] as [$a, $b, $date]) {
            foreach ([[$a, $b], [$b, $a]] as [$one, $other]) {
                FamilyRelationship::create([
                    'family_id' => $family->id,
                    'member_id' => $name($one),
                    'related_member_id' => $name($other),
                    'person_id' => $name($one),
                    'related_person_id' => $name($other),
                    'relation_type' => 'spouse',
                    'relationship_type' => 'husband',
                    'marriage_date' => $date,
                    'anniversary_date' => $date,
                    'is_active' => true,
                ]);
            }
        }

        // parent links, so the family tree can walk upward
        foreach ($members as $index => $member) {
            if (empty($member->parent_id)) {
                continue;
            }
            $parent = FamilyMember::find($member->parent_id);
            if (! $parent) {
                continue;
            }
            FamilyRelationship::create([
                'family_id' => $family->id,
                'member_id' => $parent->id,
                'related_member_id' => $member->id,
                'person_id' => $parent->id,
                'related_person_id' => $member->id,
                'relation_type' => $parent->gender === 'female' ? 'mother' : 'father',
                'relationship_type' => 'husband',
                'is_active' => true,
            ]);
        }

        // ---- feed -------------------------------------------------------
        $posts = [
            [2, 'The reunion photos are finally in! Three generations under one roof — thank you everyone for making the trip to Dhaka. Sharing the album this weekend.', 2],
            [3, "Reminder: Ammu's birthday is coming up this month. I'm collecting for a surprise lunch at her favourite restaurant. Reply here if you're in.", 1],
            [6, 'Finished the family tree cleanup on my side of the branch. Four generations connected, with dates verified against the old photo albums.', 1],
            [7, 'New chapter for me: started at the architecture studio in Gulshan this week. Grateful for all the encouragement from this family.', 0],
            [4, 'Uploaded 42 scanned photographs from the 1970s albums. Some of them have notes on the back that I have transcribed below.', 2],
            [9, 'Anyone free this Friday evening for a quick video call? Would love to catch up properly, it has been months.', 0],
        ];

        foreach ($posts as [$idx, $content, $daysAgo]) {
            $member = $members[$idx];
            FeedPost::create([
                'family_id' => $family->id,
                'user_id' => $member->user_id ?? $owner->id,
                'content' => $content,
                'type' => 'text',
                'created_at' => now()->subDays($daysAgo),
                'updated_at' => now()->subDays($daysAgo),
            ]);
        }

        $allPosts = FeedPost::all();
        $likers = [$members[2], $members[3], $members[6], $members[7], $members[9]];

        foreach ($allPosts as $post) {
            foreach ($likers as $liker) {
                if (($liker->user_id ?? null) && $liker->user_id !== $post->user_id) {
                    PostLike::firstOrCreate(['post_id' => $post->id, 'user_id' => $liker->user_id]);
                }
            }
        }

        $comments = [
            [0, 3, 'Wonderful day. The little ones had the most fun.'],
            [0, 6, 'I will add my camera roll this evening.'],
            [1, 2, 'Count me in for two — Ammu loves the biryani there.'],
            [1, 7, 'I can handle the cake.'],
            [3, 3, 'So proud of you. Well deserved.'],
            [4, 6, 'Those albums are treasure. Thank you for digitising them.'],
            [5, 9, 'Friday works for me after 8pm.'],
        ];

        foreach ($comments as [$postIdx, $memberIdx, $text]) {
            $post = $allPosts->values()[$postIdx] ?? null;
            if (! $post) {
                continue;
            }
            Comment::create([
                'post_id' => $post->id,
                'user_id' => $members[$memberIdx]->user_id ?? $owner->id,
                'comment' => $text,
            ]);
        }

        // ---- events -----------------------------------------------------
        $events = [
            [Event::TYPE_BIRTHDAY, 'Nasreen Rahman turning another year younger', now()->copy()->day(min(12, $today->daysInMonth))->setTime(19, 30), 'Home, Dhanmondi'],
            [Event::TYPE_MARRIAGE_ANNIVERSARY, 'Mizanur and Nasreen — 34 years', $anniversaryThisMonth->copy()->setTime(20, 0), 'Family dinner, Gulshan'],
            [Event::TYPE_FAMILY_REUNION, 'Annual family reunion', now()->addWeeks(3)->setTime(11, 0), 'River garden, Manikganj'],
            [Event::TYPE_DEATH_ANNIVERSARY, 'Remembering Abdul Karim Rahman', now()->addWeek()->setTime(9, 0), 'Family mosque, Old Dhaka'],
            [Event::TYPE_BIRTHDAY, 'Zara turning one this month', now()->subMonth()->setTime(12, 0), 'Home'],
        ];

        foreach ($events as [$type, $title, $date, $location]) {
            Event::create([
                'family_id' => $family->id,
                'creator_user_id' => $owner->id,
                'type' => $type,
                'title' => $title,
                'description' => 'Details will be shared in the family feed as the date approaches.',
                'event_date' => $date,
                'location' => $location,
            ]);
        }

        // ---- legacy names ----------------------------------------------
        $legacy = [
            ['Karam', 'Generosity', 'Given to my grandfather for his habit of feeding every guest who arrived without notice.', 'His mother, Zulekha', 1],
            ['Nur', 'Light', 'Carried by the eldest daughter in every generation since 1904.', 'Family tradition', 2],
            ['Rafid', 'Companion', 'Chosen so that the child would grow up surrounded by family rather than alone.', 'His grandmother', 3],
            ['Ayaan', 'Gift of God', 'Named during a difficult winter, as thanks.', 'His father', 4],
        ];

        foreach ($legacy as [$nameText, $meaning, $story, $who, $generation]) {
            LegacyName::create([
                'family_id' => $family->id,
                'name' => $nameText,
                'meaning' => $meaning,
                'story_behind_the_name' => $story,
                'who_gave_this_name' => $who,
                'family_generation' => $generation,
            ]);
        }

        // ---- messages ---------------------------------------------------
        if ($users->count() >= 3) {
            $thread = [
                [$users[1]->id, $users[0]->id, 'Bhaiya, did you see the reunion photos I posted?', 1, now()->subHours(5)],
                [$users[0]->id, $users[1]->id, 'I did — they came out beautifully. Send me the originals please.', 1, now()->subHours(4)],
                [$users[1]->id, $users[0]->id, 'I will drop them on a shared folder tonight.', 0, now()->subHours(2)],
                [$users[2]->id, $users[0]->id, 'Thanks for the birthday reminder. I can bring dessert.', 0, now()->subHour()],
            ];

            foreach ($thread as [$from, $to, $body, $read, $at]) {
                DB::table('messages')->insert([
                    'family_id' => $family->id,
                    'from_user_id' => $from,
                    'to_user_id' => $to,
                    'message' => $body,
                    'is_read' => $read,
                    'read_at' => $read ? $at : null,
                    'created_at' => $at,
                    'updated_at' => $at,
                ]);
            }
        }

        $this->command?->info('Demo family seeded: '.count($members).' members, '.$allPosts->count().' posts, '.count($events).' events.');
    }
}
