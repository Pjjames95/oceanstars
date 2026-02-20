// api/booktable.js
const nodemailer = require('nodemailer');

module.exports = async (req, res) => {
    // Set CORS headers
    res.setHeader('Access-Control-Allow-Origin', '*');
    res.setHeader('Access-Control-Allow-Methods', 'POST, OPTIONS');
    res.setHeader('Access-Control-Allow-Headers', 'Content-Type');

    // Handle preflight OPTIONS request
    if (req.method === 'OPTIONS') {
        res.status(200).end();
        return;
    }

    // Only allow POST
    if (req.method !== 'POST') {
        return res.status(405).send('Method not allowed');
    }

    try {
        console.log('Received booking request:', req.body);

        const { name, email, phone, date, time, people, message } = req.body;

        // Validate required fields
        if (!name || !email || !phone || !date || !time || !people) {
            return res.status(400).send('Please fill in all required fields');
        }

        // Validate email
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(email)) {
            return res.status(400).send('Invalid email address');
        }

        // Create email content
        const emailContent = `
You have received a new table booking request:

Name: ${name}
Email: ${email}
Phone: ${phone}
Date: ${date}
Time: ${time}
Number of People: ${people}

Message:
${message || 'No message provided'}
        `;

        console.log('Sending email...');

        // Configure nodemailer with Gmail
        const transporter = nodemailer.createTransport({
            service: 'gmail',
            auth: {
                user: process.env.EMAIL_USER,
                pass: process.env.EMAIL_PASS
            }
        });

        // Send email
        await transporter.sendMail({
            from: `"Ocean Stars Hotel" <${process.env.EMAIL_USER}>`,
            to: 'gachombajames7@gmail.com',
            replyTo: email,
            subject: `New Table Booking Request - ${name}`,
            text: emailContent
        });

        console.log('Email sent successfully');
        res.status(200).send('OK');

    } catch (error) {
        console.error('Error:', error);
        res.status(500).send('Message could not be sent. Please try again later.');
    }
};