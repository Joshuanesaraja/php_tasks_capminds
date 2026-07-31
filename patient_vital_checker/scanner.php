<?php

function scanFolder($path)
{
    $items = scandir($path);

    foreach ($items as $item)
    {

        if ($item == "." || $item == "..")
        {
            continue;
        }

        $fullPath = $path . DIRECTORY_SEPARATOR . $item;
        // DIRECTORY_SEPARATOR such as / \
        if (is_dir($fullPath))
        // this checks whether the current item is a folder
        {
            echo "Folder : " . $item . "<br>";

            scanFolder($fullPath); 
            // recursion
        }
        else
        {
            echo "File : " . $item . "<br>";
        }
    }
}

?>