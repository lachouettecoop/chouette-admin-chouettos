<?php
/**
 * Converts data from an export to "ready to import" SQL
 *
 * Usage:
 *   docker-composer run --rm -w /tools php bash -c "cat in.csv | php -f raw-sql-from-csv.php"
 */

function dateToSQL($frenchDate) {
    $date = \DateTime::createFromFormat('d/m/Y', $frenchDate);
    return $date ? $date->format('Y-m-d') : null;
}

function userDataOf($line) {
    $userData = array_intersect_key($line, array_flip([
        'civilite',
        'nom',
        'prenom',
        'dateAdhesion',
        'codebarre',
        'csp',
        'domaineCompetence',
        'telephone',
        'accepteMail',
        'email',
        'enabled',
    ]));
    $userData['dateNaissance'] = dateToSql($line['exportDateNaissance']);
    $userData['username'] = $userData['email'];
    $userData['username_canonical'] = $userData['email'];
    $userData['email_canonical'] = $userData['email'];
    return $userData;
}

function addressDataOf($line) {
    return [
        'destinataire' => $line['exportAdresse'],
        'ligne1' => $line['exportAdresse1'],
        'ligne2' => $line['exportAdresse2'],
        'ligne3' => $line['exportAdresse3'],
        'codePostal' => $line['exportAdresse4'],
        'ville' => $line['exportAdresse5'],
        'pays' => $line['exportAdresse6'],
    ];
}

function adhesionsDataOf($line) {
    $exportedValues = function($value) {
        return array_map('trim', explode(',', trim($value, ',')));
    };
    return array_map(
        function($annee, $dateAdhesion, $montant) {
            return compact('annee', 'dateAdhesion', 'montant');
        },
        $exportedValues($line['exportdAhesionAnnee']),
        array_map('dateToSql', $exportedValues($line['exportAdhesionDate'])),
        $exportedValues($line['exportAdhesionMontant'])
    );
}

function sqlCreationQueryFor($chouettos) {
    $keysOf = function($data) {
        return implode(',', array_keys($data));
    };
    $valuesOf = function($data) {
        return implode(',', array_map(function($val) {
            return '"'. $val . '"';
        }, array_values($data)));
    };

    $insertUserQuery = <<<SQL
    INSERT INTO
        fos_user ({$keysOf($chouettos['user'])})
        VALUES({$valuesOf($chouettos['user'])});
    SET @user_id = LAST_INSERT_ID();

    INSERT INTO
        addresse ({$keysOf($chouettos['address'])})
        VALUES({$valuesOf($chouettos['address'])});
    SET @addresse_id = LAST_INSERT_ID();

    INSERT INTO user_address (user_id, addresse_id) VALUES (@user_id, @addresse_id);
SQL;

    return $insertUserQuery;
}

// Dirty bit!

$source = fopen('php://stdin', 'r+');
$headers = fgetcsv($source);
$defaults = array_fill_keys($headers, '');

$lines = [];
while($line = fgetcsv($source)) {
    $lines[] = array_merge($defaults, array_combine($headers, $line));
}

$chouettos = array_map(function ($line) {
    return [
        'user' => userDataOf($line),
        'address' => addressDataOf($line),
        'adhesions' => adhesionsDataOf($line)
    ];
}, $lines);

$out = fopen('php://stdout', 'w+');
fwrite($out, implode(";\n", array_map('sqlCreationQueryFor', $chouettos)));
