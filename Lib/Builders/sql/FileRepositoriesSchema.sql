/*
 * This file is part of the Eulogix\Cool package.
 *
 * (c) Eulogix <http://www.eulogix.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
*/

CREATE TABLE IF NOT EXISTS [[ globalSchemaName ]]_files (
    file_id serial not null,

    parent_id integer, -- unused for now

    source_table_id integer,
    source_table TEXT,
    category TEXT,

    file_name TEXT,
    file_size integer,
    upload_date timestamp,
    last_modification_date timestamp,
    uploaded_by_user integer,

    checksum_sha1 TEXT,

    properties JSON,

	CONSTRAINT [[ globalSchemaName ]]_files_pkey PRIMARY KEY (file_id),

	CONSTRAINT [[ globalSchemaName ]]_files_pkey_fk_1 FOREIGN KEY (parent_id)
      REFERENCES [[ globalSchemaName ]]_files (file_id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE CASCADE
);

CREATE INDEX files_idx_1 ON [[ globalSchemaName ]]_files (source_table_id);
CREATE INDEX files_idx_2 ON [[ globalSchemaName ]]_files (source_table);
CREATE INDEX files_idx_3 ON [[ globalSchemaName ]]_files (category);
CREATE INDEX files_idx_4 ON [[ globalSchemaName ]]_files (file_name);
CREATE INDEX files_idx_5 ON [[ globalSchemaName ]]_files (file_size);
CREATE INDEX files_idx_6 ON [[ globalSchemaName ]]_files (upload_date);
CREATE INDEX files_idx_7 ON [[ globalSchemaName ]]_files (last_modification_date);
CREATE INDEX files_idx_8 ON [[ globalSchemaName ]]_files (uploaded_by_user);
CREATE INDEX files_idx_9 ON [[ globalSchemaName ]]_files (checksum_sha1);

{{% for tableName,tableMap in tableMaps %}}
    {{% set fileCategories = tableMap.getFileCategories() %}}
    {{% if fileCategories|length > 0 %}}

    CREATE FUNCTION [[ tableMap.getCoolRawName() ]]_files_delete() RETURNS trigger
        LANGUAGE plpgsql
        AS $$
    BEGIN
        PERFORM format('DELETE FROM [[ globalSchemaName ]]_files WHERE source_table=%1$L AND source_table_id=%2$L', '[[ tableMap.getCoolRawName() ]]', OLD.[[ tableMap.getPkFields()[0] ]] );
        RETURN OLD;
    END;
    $$;

    CREATE TRIGGER [[ tableMap.getCoolRawName() ]]_files_delete_trg
    AFTER DELETE ON [[ tableMap.getCoolRawName() ]]
        FOR EACH ROW
        EXECUTE PROCEDURE [[ tableMap.getCoolRawName() ]]_files_delete();

    {{% endif %}}
{{% endfor %}}