const nodemailer = require('nodemailer');

module.exports = async (req, res) => {
    // Enable CORS
    res.setHeader('Access-Control-Allow-Origin', '*');
    res.setHeader('Access-Control-Allow-Methods', 'POST, OPTIONS');
    res.setHeader('Access-Control-Allow-Headers', 'Content-Type');

    if (req.method === 'OPTIONS') {
        res.status(200).end();
        return;
    }

    if (req.method !== 'POST') {
        res.status(405).json({ error: 'Method not allowed' });
        return;
    }

    try {
        const { name, email, subject, message } = req.body;

        // Validate
        if (!name || !email || !message) {
            res.status(400).send('Please fill in all required fields');
            return;
        }

        // Configure transporter
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
            subject: subject || 'New Contact Form Message',
            text: `
Name: ${name}
Email: ${email}
Subject: ${subject || 'No subject'}

Message:
${message}
            `
        });

        res.status(200).send('OK');

    } catch (error) {
        console.error('Error:', error);
        res.status(500).send('Message could not be sent. Please try again later.');
    }
};