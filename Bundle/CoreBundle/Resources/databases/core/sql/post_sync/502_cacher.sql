DROP TABLE IF EXISTS temp.main_cacher CASCADE;

CREATE TABLE temp.main_cacher
                    (
		     c_key BIGINT,
		     c_value JSON,
		     expiration_date TIMESTAMP,
			PRIMARY KEY (c_key)
                    );

CREATE INDEX main_cacher_idx ON temp.main_cacher (expiration_date);