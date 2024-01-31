<?php

$apiUrl = "https://pokeapi.co/api/v2/type";
$typeData = file_get_contents($apiUrl);

if ($typeData !== false) {
    $data = json_decode($typeData, true);

    foreach ($data['results'] as $type) {
        $typeName = $type['name'];

        // Récupérer l'image du type
        $typeDetailsUrl = $type['url'];
        $typeDetailsData = file_get_contents($typeDetailsUrl);

        if ($typeDetailsData !== false) {
            $typeDetails = json_decode($typeDetailsData, true);

            // Récupérer le nom en anglais
            $englishName = $typeDetails['names'][1]['name'];

            // Vérifier si la clé 'type' est définie
            if (isset($typeDetails['type']['url'])) {
                $typeImageUrl = $typeDetails['type']['url'];

                // Télécharger l'image et la sauvegarder avec le nom en anglais
                $imageContent = file_get_contents($typeImageUrl);

                if ($imageContent !== false) {
                    file_put_contents("type_images/{$englishName}.png", $imageContent);
                    echo "Téléchargé : {$englishName}\n";
                } else {
                    echo "Erreur lors du téléchargement de l'image du type : {$englishName}\n";
                }
            } else {
                echo "Avertissement : La clé 'type' n'est pas définie pour le type : {$typeName}\n";
            }
        } else {
            echo "Erreur lors de la récupération des détails du type : {$typeName}\n";
        }
    }

    echo "Téléchargement des images des types terminé !";
} else {
    echo "Erreur lors de la récupération des données de l'API.\n";
}
?>
