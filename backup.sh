#!/bin/bash

# Caminho absoluto da aplicação no servidor
APP_DIR="/var/www/asa-app"
RCLONE_REMOTE="gdrive"
RCLONE_DEST_FOLDER="BackupsASA"
LOCAL_BACKUP_DIR="/home/ubuntu/backups"
DATE=$(date +%Y-%m-%d)
BACKUP_FILENAME="asa-backup-$DATE.tar.gz"
BACKUP_FILEPATH="$LOCAL_BACKUP_DIR/$BACKUP_FILENAME"

echo "=== Iniciando Backup da Aplicação ASA [$(date)] ==="

mkdir -p "$LOCAL_BACKUP_DIR"

if [ ! -d "$APP_DIR" ]; then
    echo "Erro: O diretório do app '$APP_DIR' não existe!"
    exit 1
fi

echo "Compactando banco de dados e imagens..."
tar -czf "$BACKUP_FILEPATH" -C "$APP_DIR" database/database.sqlite storage/app/public

if [ $? -eq 0 ] && [ -f "$BACKUP_FILEPATH" ]; then
    echo "Compactação concluída com sucesso: $BACKUP_FILENAME ($(du -sh "$BACKUP_FILEPATH" | cut -f1))"
else
    echo "Erro ao criar o arquivo de backup compactado!"
    exit 1
fi

echo "Enviando backup para o Google Drive ($RCLONE_REMOTE:$RCLONE_DEST_FOLDER)..."
rclone copy "$BACKUP_FILEPATH" "$RCLONE_REMOTE:$RCLONE_DEST_FOLDER"

if [ $? -eq 0 ]; then
    echo "Backup enviado com sucesso para o Google Drive!"
else
    echo "Erro ao enviar o backup para o Google Drive via Rclone!"
    exit 1
fi

echo "Limpando backups locais antigos (mais de 2 dias)..."
find "$LOCAL_BACKUP_DIR" -name "asa-backup-*.tar.gz" -type f -mtime +2 -delete

echo "Limpando backups antigos no Google Drive (mais de 7 dias)..."
rclone delete "$RCLONE_REMOTE:$RCLONE_DEST_FOLDER" --min-age 7d

echo "=== Backup Concluído com Sucesso! [$(date)] ==="
