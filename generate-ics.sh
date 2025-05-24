#!/bin/bash

TARGET_DIR="dev/caldav/data/collections/collection-root/dev/example"
mkdir -p "$TARGET_DIR"

TIMES=("09:00" "11:00" "14:00" "16:00")
START_DATE=$(date -u +"%Y-%m-%d")

# Clean up old ICS files
find "$TARGET_DIR" -name "meeting-*.ics" -delete

counter=1

for day in $(seq 0 29); do
  DATE=$(date -u -j -v+"${day}"d -f "%Y-%m-%d" "$START_DATE" +"%Y%m%d")

  for idx in "${!TIMES[@]}"; do
    TIME="${TIMES[$idx]}"
    START_UTC=$(date -u -j -f "%Y%m%d %H:%M" "${DATE} ${TIME}" +"%Y%m%dT%H%M%SZ")
    END_UTC=$(date -u -j -v+30M -f "%Y%m%d %H:%M" "${DATE} ${TIME}" +"%Y%m%dT%H%M%SZ")
    DTSTAMP=$(date -u +"%Y%m%dT%H%M%SZ")

    ID_PADDED=$(printf "%03d" "$counter")
    EVENT_UID="meeting-${ID_PADDED}@examplecorp.com"
    FILENAME="$TARGET_DIR/${EVENT_UID}.ics"

    {
      echo "BEGIN:VCALENDAR"
      echo "VERSION:2.0"
      echo "PRODID:-//Radicale Dev//EN"
      echo "BEGIN:VEVENT"
      echo "UID:$EVENT_UID"
      echo "DTSTART:$START_UTC"
      echo "DTEND:$END_UTC"
      echo "DTSTAMP:$DTSTAMP"
      echo "ORGANIZER;CN=Alex Johnson:mailto:alex.johnson@examplecorp.com"
      echo "ATTENDEE;CN=Jamie Smith;PARTSTAT=ACCEPTED:mailto:jamie.smith@examplecorp.com"
      echo "SUMMARY:1:1 with Jamie Smith"
      echo "END:VEVENT"
      echo "END:VCALENDAR"
    } > "$FILENAME"

    echo "[+] Created $FILENAME"
    counter=$((counter + 1))
  done
done

echo "[✔] All .ics files generated in $TARGET_DIR"
