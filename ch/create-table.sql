CREATE TABLE default.search_results
(
    search_id UInt32 NOT NULL,
    search_word  String NOT NULL,
    product_name  String NOT NULL,
    product_id UInt32 NOT NULL,
    brand_name String NOT NULL
)
ENGINE = MergeTree()
PRIMARY KEY (search_id);