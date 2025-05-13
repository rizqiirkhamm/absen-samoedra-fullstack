#!/bin/sh

# Auto Checkout Script
#
# Script shell ini akan menjalankan file auto_checkout_command.php
# Dijalankan oleh cron job setiap hari pada jam 5 sore

# Tentukan jalur direktori aplikasi
APP_DIR="/home/u909411809/domains/contohdomain.xyz/public_html"
LOG_FILE="$APP_DIR/storage/logs/auto_checkout.log"

# Catat bahwa script dijalankan
echo "[$(date '+%Y-%m-%d %H:%M:%S')] Menjalankan auto_checkout_command.php" >> $LOG_FILE

# Jalankan PHP script
/usr/bin/php $APP_DIR/auto_checkout_command.php >> $LOG_FILE 2>&1

# Catat status exit
EXIT_CODE=$?
if [ $EXIT_CODE -eq 0 ]; then
    echo "[$(date '+%Y-%m-%d %H:%M:%S')] auto_checkout_command.php berhasil dijalankan" >> $LOG_FILE
else
    echo "[$(date '+%Y-%m-%d %H:%M:%S')] Error saat menjalankan auto_checkout_command.php (kode: $EXIT_CODE)" >> $LOG_FILE
fi

exit $EXIT_CODE
