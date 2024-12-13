<?php
// Function to read JSON input from a file
function getInputFromFile($filePath) {
    if (!file_exists($filePath)) {
        echo "Error: File not found at $filePath\n";
        exit(1);
    }

    $data = file_get_contents($filePath);
    return json_decode($data, true)['leads'];
}

// Function to deduplicate records
function deduplicate($records) {
    $id_map = []; // Track records by _id
    $email_map = []; // Track records by email
    $changes_log = []; // Store change logs

    foreach ($records as $record) {
        $record_id = $record['_id'];
        $record_email = $record['email'];

        // Resolve by _id
        if (isset($id_map[$record_id])) {
            [$id_map[$record_id], $log] = resolveDuplicate($id_map[$record_id], $record);
            $changes_log = array_merge($changes_log, $log);
        } else {
            $id_map[$record_id] = $record;
        }

        // Resolve by email only if not already handled by _id
        if (!isset($id_map[$record_id]) && isset($email_map[$record_email])) {
            [$email_map[$record_email], $log] = resolveDuplicate($email_map[$record_email], $record);
            $changes_log = array_merge($changes_log, $log);
        } else {
            $email_map[$record_email] = $record;
        }
    }

    return [array_values($id_map), $changes_log];
}

// Function to resolve duplicates
function resolveDuplicate($existing, $new) {
    $logs = [];
    $existing_date = new DateTime($existing['entryDate']);
    $new_date = new DateTime($new['entryDate']);

    if ($new_date > $existing_date) {
        $logs = generateChangeLog($existing, $new);
        return [$new, $logs];
    } elseif ($new_date == $existing_date) {
        $logs = generateChangeLog($existing, $new);
        return [$new, $logs];
    }
    return [$existing, $logs];
}

// Function to generate change log
function generateChangeLog($existing, $new) {
    $log = [];
    foreach ($existing as $key => $value) {
        if ($existing[$key] != $new[$key]) {
            $log[] = [
                'field' => $key,
                'from' => $existing[$key],
                'to' => $new[$key]
            ];
        }
    }
    return $log;
}

// Function to save output to files
function saveOutputToFile($deduplicatedRecords, $changeLog, $deduplicatedFilePath, $changeLogFilePath) {
    file_put_contents($deduplicatedFilePath, json_encode(['leads' => $deduplicatedRecords], JSON_PRETTY_PRINT));
    file_put_contents($changeLogFilePath, json_encode($changeLog, JSON_PRETTY_PRINT));
}

// Main program
function main() {
    $inputFile = 'leads.json'; // Input file path
    $deduplicatedFile = 'deduplicated_leads.json'; // Output file path for deduplicated records
    $changeLogFile = 'change_log.json'; // Output file path for change log

    $records = getInputFromFile($inputFile);
    [$deduplicated_records, $changes_log] = deduplicate($records);

    saveOutputToFile($deduplicated_records, $changes_log, $deduplicatedFile, $changeLogFile);

    echo "Deduplication complete.\n";
    echo "Deduplicated records saved to: $deduplicatedFile\n";
    echo "Change log saved to: $changeLogFile\n";
}

main();
?>
