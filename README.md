run server locally without apache:

```sh
$ sh runserver.sh
```


get a password hash

```sh
$ cat tests/hash.sh 
curl http://localhost:9999/gethash -d '{"password":"PASS"}'

$ sh tests/hash.sh 
$2y$10$ol7bygUslBeEOqv5/IGVPOpquREgjtofFpCDbDH0y1KXhEIeXmy1S
```

verify a password hash

```sh
$ cat tests/verify_hash.sh 
curl http://localhost:9999/hash -d '{"password":"mypassword12345","hash":"$2y$10$FMIfym7WO2PxUWSvml/2i.QN6o3dyIRRoOV1Uf9mqZTNKMClfNQb."}'

$ sh tests/verify_hash.sh 
MATCH
```

parse a url for post types

```sh
# note: python json.tool for json formatting.

$ cat tests/scraper.sh 
curl http://localhost:9999/scraper -d '{"query":"https://www.zerohedge.com/political/why-hr1-threatens-election-integrity"}' | python3 -m json.tool

$ sh tests/scraper.sh 
{
    "source_url": "https://www.zerohedge.com/political/why-hr1-threatens-election-integrity",
    "source_title": "Why HR1 Threatens Election Integrity",
    "source_text": "Much ink has been spilled warning of the ramifications should Democrats pass their election \u201creform\u201d package, HR1 -- and for good reason, given how the bill would upend our nation\u2019s electoral system.",
    "source_type": "link",
    "source_host": "www.zerohedge.com",
    "source_thumbnail": "https://zh-prod-1cc738ca-7d3b-4a72-b792-20bd8d8fa069.storage.googleapis.com/s3fs-public/styles/16_9_max_700/public/2021-03/6046687aa42a2.image_.jpg?itok=HWMBia9v"
}

```
