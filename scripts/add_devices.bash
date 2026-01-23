#!/bin/bash

while IFS= read -r hostname; do
    # skip empty lines
    [[ -z "$hostname" ]] && continue

    ./add_device.php "$hostname" h6of21oju v2c 161 udp
done < devices.txt