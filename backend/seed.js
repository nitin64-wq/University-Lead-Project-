const mongoose = require('mongoose');
const dotenv = require('dotenv');
const User = require('./models/User');

dotenv.config();

const seedUsers = async () => {
    try {
        await mongoose.connect(process.env.MONGO_URI);
        console.log('MongoDB Connected for seeding...');

        // Clear existing users
        await User.deleteMany({});

        // Create admin
        await User.create({
            name: 'Super Admin',
            email: 'admin@university.edu',
            password: 'admin123',
            role: 'admin',
            phone: '9999999999',
        });

        // Create counsellors
        const counsellors = [
            {
                name: 'Rahul Sharma',
                email: 'rahul@university.edu',
                password: 'rahul123',
                role: 'counsellor',
                phone: '9876543210',
            },
            {
                name: 'Priya Patel',
                email: 'priya@university.edu',
                password: 'priya123',
                role: 'counsellor',
                phone: '9876543211',
            },
            {
                name: 'Aman Singh',
                email: 'aman@university.edu',
                password: 'aman123',
                role: 'counsellor',
                phone: '9876543212',
            },
        ];

        for (const counsellor of counsellors) {
            await User.create(counsellor);
        }

        console.log('✅ Seed data created successfully!');
        console.log('\nDefault Credentials:');
        console.log('Admin: admin@university.edu / admin123');
        console.log('Counsellor: rahul@university.edu / rahul123');
        console.log('Counsellor: priya@university.edu / priya123');
        console.log('Counsellor: aman@university.edu / aman123');
        process.exit(0);
    } catch (error) {
        require('fs').writeFileSync('error_log.txt', error.stack || error.message, 'utf8');
        console.error('Seed error recorded to error_log.txt');
        process.exit(1);
    }
};

seedUsers();
