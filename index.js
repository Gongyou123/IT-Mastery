const express = require('express');
const multer = require('multer');
const app = express();

const upload = multer({
    dest: 'uploads/', // Save uploaded files to this directory
    limits: {
        fileSize: 1000000 // Max file size in bytes (1 MB)
    },
    fileFilter(req, file, cb) {
        // Reject file types that are not images
        if (!file.originalname.match(/\.(jpg|jpeg|png|gif)$/)) {
            return cb(new Error('Please upload an image file'));
        }
        cb(undefined, true);
    }
});

app.post('/upload', upload.single('file'), (req, res) => {
    res.redirect('/uploads');
});

app.get('/uploads', (req, res) => {
    // Send a list of uploaded files to the client
});

app.listen(3000, () => {
    console.log('Server listening on port 3000');
});