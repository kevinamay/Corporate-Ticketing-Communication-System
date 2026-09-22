<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\Message;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Create Departments
        $itDept = Department::create([
            'name' => 'IT Support',
            'icon' => 'laptop',
            'description' => 'Technical assistance, network, hardware and corporate systems',
        ]);

        $hrDept = Department::create([
            'name' => 'Human Resources',
            'icon' => 'users',
            'description' => 'Employee relations, payroll, leave requests and recruitment',
        ]);

        $maintDept = Department::create([
            'name' => 'Facility & Maintenance',
            'icon' => 'wrench',
            'description' => 'Building repairs, air conditioning, electrical and workplace safety',
        ]);

        // 2. Create Users
        $password = Hash::make('password123');

        $alice = User::create([
            'name' => 'Alice Johnson',
            'email' => 'alice@company.com',
            'password' => $password,
            'department_id' => $hrDept->id,
            'role' => 'staff',
            'avatar' => 'https://images.unsplash.com/photo-1494790108377-be9c29b29330?w=150',
        ]);

        $bob = User::create([
            'name' => 'Bob Smith',
            'email' => 'bob@company.com',
            'password' => $password,
            'department_id' => $itDept->id,
            'role' => 'agent',
            'avatar' => 'https://images.unsplash.com/photo-1535713875002-d1d0cf377fde?w=150',
        ]);

        $clara = User::create([
            'name' => 'Clara Davis',
            'email' => 'clara@company.com',
            'password' => $password,
            'department_id' => $hrDept->id,
            'role' => 'agent',
            'avatar' => 'https://images.unsplash.com/photo-1580489944761-15a19d654956?w=150',
        ]);

        $david = User::create([
            'name' => 'David Miller',
            'email' => 'david@company.com',
            'password' => $password,
            'department_id' => $maintDept->id,
            'role' => 'agent',
            'avatar' => 'https://images.unsplash.com/photo-1570295999919-56ceb5ecca61?w=150',
        ]);

        $admin = User::create([
            'name' => 'Sarah Connor',
            'email' => 'admin@company.com',
            'password' => $password,
            'department_id' => null,
            'role' => 'admin',
            'avatar' => 'https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?w=150',
        ]);

        // 3. Create Tickets & Initial Conversations
        // Ticket 1: IT Support
        $t1 = Ticket::create([
            'sender_id' => $alice->id,
            'target_department_id' => $itDept->id,
            'title' => 'VPN connection drops intermittently on macOS Sequoia',
            'category' => 'IT',
            'description' => 'Whenever I try connecting to the regional internal portal via Cisco AnyConnect, the connection drops every 5 minutes. Tried restarting but still recurring.',
            'priority' => 'High',
            'status' => 'In Progress',
            'created_at' => now()->subHours(3),
        ]);

        Message::create([
            'ticket_id' => $t1->id,
            'user_id' => $alice->id,
            'message' => "Hi IT team, I'm experiencing constant VPN timeouts when opening internal HR tools. Could someone check the gateway logs?",
            'created_at' => now()->subHours(3),
        ]);

        Message::create([
            'ticket_id' => $t1->id,
            'user_id' => $bob->id,
            'message' => "Hello Alice! I see multiple reconnect attempts from your IP. Can you verify if you're connected via office Wi-Fi or home network?",
            'created_at' => now()->subHours(2)->subMinutes(30),
        ]);

        Message::create([
            'ticket_id' => $t1->id,
            'user_id' => $alice->id,
            'message' => "I'm on office 5GHz Wi-Fi, near conference room B on 3rd floor.",
            'created_at' => now()->subHours(2)->subMinutes(10),
        ]);

        Message::create([
            'ticket_id' => $t1->id,
            'user_id' => $bob->id,
            'message' => 'Understood! We had an access point handshake threshold issue there. I pushed a certificate patch to your device. Try reconnecting now!',
            'created_at' => now()->subHours(1),
        ]);

        // Ticket 2: Facility & Maintenance
        $t2 = Ticket::create([
            'sender_id' => $bob->id,
            'target_department_id' => $maintDept->id,
            'title' => 'Server Room B AC unit leaking water',
            'category' => 'Maintenance',
            'description' => 'Moisture sensors triggered underneath AC Unit 2 in Server Room B. Water pooling spotted near rack 4. Urgent inspection required.',
            'priority' => 'Critical',
            'status' => 'Open',
            'created_at' => now()->subHours(1),
        ]);

        Message::create([
            'ticket_id' => $t2->id,
            'user_id' => $bob->id,
            'message' => 'Moisture sensors triggered in rack 4. Please send an HVAC technician immediately before hardware damage happens.',
            'created_at' => now()->subHours(1),
        ]);

        Message::create([
            'ticket_id' => $t2->id,
            'user_id' => $david->id,
            'message' => 'En route right now with portable vacuum pumps and replacement drainage valve. ETA 5 minutes.',
            'created_at' => now()->subMinutes(35),
        ]);

        // Ticket 3: HR Operations
        $t3 = Ticket::create([
            'sender_id' => $alice->id,
            'target_department_id' => $hrDept->id,
            'title' => 'Annual Leave rollover inquiry for Q4',
            'category' => 'HR',
            'description' => 'Need clarification on maximum allowable carryover days into 2027 fiscal year under new policy.',
            'priority' => 'Low',
            'status' => 'Pending',
            'created_at' => now()->subDay(),
        ]);

        Message::create([
            'ticket_id' => $t3->id,
            'user_id' => $alice->id,
            'message' => 'Hi Clara, could you please confirm how many unused leave days can be rolled over to next year?',
            'created_at' => now()->subDay(),
        ]);

        Message::create([
            'ticket_id' => $t3->id,
            'user_id' => $clara->id,
            'message' => 'Hi Alice! Under the 2026 handbook, up to 5 days roll over automatically. Any excess requires manager endorsement by Nov 30.',
            'created_at' => now()->subHours(18),
        ]);
    }
}
