// api/contact.js
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
        return res.status(405).json({ error: 'Method not allowed' });
    }

    try {
        console.log('Contact form received:', req.body);

        const { name, email, subject, message } = req.body;

        // Validate required fields
        if (!name || !email || !message) {
            return res.status(400).send('Please fill in all required fields');
        }

        // Validate email
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(email)) {
            return res.status(400).send('Invalid email address');
        }

        // Check environment variables
        if (!process.env.EMAIL_USER || !process.env.EMAIL_PASS) {
            console.error('Missing email credentials');
            return res.status(500).send('Server configuration error');
        }

        // Create email content
        const emailContent = `
You have received a new contact form message:

Name: ${name}
Email: ${email}
Subject: ${subject || 'No subject'}

Message:
${message}
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
        const info = await transporter.sendMail({
            from: `"Ocean Stars Hotel" <${process.env.EMAIL_USER}>`,
            to: 'gachombajames7@gmail.com',
            replyTo: email,
            subject: subject ? `Contact: ${subject}` : 'New Contact Form Message',
            text: emailContent
        });

        console.log('Email sent:', info.messageId);
        res.status(200).send('OK');

    } catch (error) {
        console.error('Detailed error:', error);
        
        // Send more specific error message
        if (error.code === 'EAUTH') {
            res.status(500).send('Email authentication failed. Check credentials.');
        } else if (error.code === 'ENOTFOUND') {
            res.status(500).send('Network error. Could not connect to email server.');
        } else {
            res.status(500).send('Message could not be sent. Please try again later.');
        }
    }
};