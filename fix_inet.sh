#!/bin/bash
export PGPASSWORD='flo$CarlErnesto'
psql -h localhost -p 5433 -U florenceegi -d florenceegi_dev << 'EOF'
ALTER TABLE user_activities ALTER COLUMN ip_address TYPE varchar(45);
\q
EOF
echo "Done with exit code: $?"
