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