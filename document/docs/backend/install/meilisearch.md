# Meilisearch 本地安装

### apt 安装 
```shell

echo "deb [trusted=yes] https://apt.fury.io/meilisearch/ /" | sudo tee /etc/apt/sources.list.d/fury.list

sudo apt update && sudo apt install meilisearch

# Launch Meilisearch
meilisearch
```
### 配置 config 文件 /etc/meilisearch/config.toml
```ini
db_path = "./data.ms"
env = "production"
http_addr = "127.0.0.1:7700"
master_key = "ibisaas-ibi603613"
# no_analytics = true
http_payload_size_limit = "100 MB"
log_level = "INFO"
# max_indexing_memory = "2 GiB"
# max_indexing_threads = 4
dump_dir = "dumps/"
# import_dump = "./path/to/my/file.dump"
ignore_missing_dump = false
ignore_dump_if_db_exists = false
schedule_snapshot = false
snapshot_dir = "snapshots/"
# import_snapshot = "./path/to/my/snapshot"
ignore_missing_snapshot = false
ignore_snapshot_if_db_exists = false
# ssl_auth_path = "./path/to/root"
# ssl_cert_path = "./path/to/certfile"
# ssl_key_path = "./path/to/private-key"
# ssl_ocsp_path = "./path/to/ocsp-file"
ssl_require_auth = false
ssl_resumption = false
ssl_tickets = false
experimental_enable_metrics = false
experimental_reduce_indexing_memory_usage = false
# experimental_max_number_of_batched_tasks = 100
```
### 配置 service config
```shell
    echo "[Unit]
          Description =meilisearch service
          After = network.target syslog.target
          Wants = network.target
    
    [Service]
    Type = simple
    ExecStart = /usr/bin/meilisearch --config-file-path /etc/meilisearch/config.toml
    
    [Install]
    WantedBy = multi-user.target" > /etc/systemd/system/meilisearch.service
```

### 启动服务
```shell
    sudo systemctl daemon-reload
    sudo systemctl start meilisearch
    sudo systemctl status meilisearch
```
