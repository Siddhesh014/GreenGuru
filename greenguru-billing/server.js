const express = require('express');
const mysql = require('mysql2');
const bodyParser = require('body-parser');
const path = require('path');

const app = express();
const PORT = process.env.PORT || 3000;

// Middleware
app.use(bodyParser.urlencoded({ extended: true }));
app.use(bodyParser.json());

// Serve static files from the 'public' directory
app.use(express.static(path.join(__dirname, 'public')));

// MySQL Connection with retry logic
const dbConfig = {
    host: process.env.DB_HOST || 'localhost',
    user: process.env.DB_USER || 'root',
    password: process.env.DB_PASSWORD || '',
    database: process.env.DB_NAME || 'project'
};

let connection;

function connectWithRetry() {
    connection = mysql.createConnection(dbConfig);
    connection.connect((err) => {
        if (err) {
            console.error('Database connection failed, retrying in 3s...', err.message);
            setTimeout(connectWithRetry, 3000);
        } else {
            console.log('Connected to the database');
        }
    });
    connection.on('error', (err) => {
        if (err.code === 'PROTOCOL_CONNECTION_LOST' || err.fatal) {
            console.error('Database connection lost, reconnecting...');
            connectWithRetry();
        }
    });
}

connectWithRetry();

// Route to handle form submission
app.post('/submit', (req, res) => {
    const { fullName, email, address, city, country, postalCode, cardNumber, expiryDate, cvv } = req.body;

    const query = `INSERT INTO billing_info (full_name, email, address, city, country, postal_code, card_number, expiry_date, cvv)
                   VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)`;

    connection.query(query, [fullName, email, address, city, country, postalCode, cardNumber, expiryDate, cvv], (err, results) => {
        if (err) {
            console.error(err);
            return res.status(500).send('Error inserting data.');
        }
        res.send({ success: true, message: 'Order placed successfully!' });
    });
});

app.listen(PORT, () => {
    console.log(`Server is running on http://localhost:${PORT}`);
});
