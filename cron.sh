cd /home/bitto/aspire.bittosolution.vn/backend-service && git stash && git pull && git stash clear
cd /home/bitto/aspire.bittosolution.vn/ftp-service && git stash && git pull && git stash clear

chmod -R 750 /home/bitto/aspire.bittosolution.vn
chown -R bitto:www /home/bitto/aspire.bittosolution.vn
chmod -R 770 /home/bitto/aspire.bittosolution.vn/backend-service/public/images
chmod -R 770 /home/bitto/aspire.bittosolution.vn/backend-service/storage
chmod -R 770 /home/bitto/aspire.bittosolution.vn/ftp-service/storage
chmod -R 770 /home/bitto/aspire.bittosolution.vn/ftp-service/uploads
