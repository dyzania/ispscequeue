<?php
header('Content-Type: application/json');

// Function to extract text from DOCX
function readDocx($filename) {
    $content = '';
    $zip = new ZipArchive;
    
    if ($zip->open($filename) === TRUE) {
        // Read the main XML file
        if (($index = $zip->locateName('word/document.xml')) !== false) {
            $xml = $zip->getFromIndex($index);
            $dom = new DOMDocument;
            $dom->loadXML($xml, LIBXML_NOENT | LIBXML_XINCLUDE | LIBXML_NOERROR | LIBXML_NOWARNING);
            
            // Get all text nodes
            $content = strip_tags($dom->saveXML());
        }
        $zip->close();
    }
    return $content;
}

// Function to extract text from PDF (Basic Parser)
function readPdf($filename) {
    // Basic text extraction without heavy libraries
    // This is not perfect but works for simple text-based PDFs
    $content = file_get_contents($filename);
    
    // Extract text streams
    $text = '';
    if (preg_match_all('/BT[\s\r\n]+([\s\S]+?)[\s\r\n]+ET/', $content, $matches)) {
        foreach ($matches[1] as $block) {
            // Extract actual text content (strings in parentheses)
            if (preg_match_all('/\((.*?)\)/', $block, $text_matches)) {
                foreach ($text_matches[1] as $tm) {
                    $text .= $tm . ' ';
                }
            }
        }
    }
    
    // Clean up
    $text = str_replace(['\\(', '\\)'], ['(', ')'], $text); // Fix escaped parens
    $text = preg_replace('/\\\\[0-7]{3}/', '', $text); // Remove octal codes
    
    // If regex failed (compressed streams), try raw string extraction as backup
    if (empty(trim($text))) {
        // Remove binary/control characters, keep printable text
        $text = preg_replace('/[^[:print:]\r\n\t]/', '', $content);
        // Remove PDF structure keywords
        $pdf_keywords = ['obj', 'endobj', 'stream', 'endstream', 'PDF', 'EOF', 'xref', 'trailer', 'startxref'];
        $text = str_replace($pdf_keywords, '', $text);
    }

    return trim($text);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
        echo json_encode(['error' => 'No file uploaded or upload error.']);
        exit;
    }

    $file = $_FILES['file'];
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $text = '';

    try {
        if ($ext === 'docx') {
            $text = readDocx($file['tmp_name']);
        } elseif ($ext === 'pdf') {
            $text = readPdf($file['tmp_name']);
        } else {
            echo json_encode(['error' => 'Unsupported file type. Please upload PDF or DOCX.']);
            exit;
        }

        echo json_encode(['text' => $text]);

    } catch (Exception $e) {
        echo json_encode(['error' => 'Extraction failed: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['error' => 'Invalid request method.']);
}
