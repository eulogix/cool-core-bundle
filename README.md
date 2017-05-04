CoolCoreBundle
=====

Provides functionalities to quickly build CRUD (but not only) applications  with Symfony, PostgreSQL and Dojo.
It integrates the following key technologies

- Symfony2
- Postgres 9.x (with plv8)
- Dojo Toolkit

optional:

- Activiti BPMN engine
- Rundeck scheduler
- Pentaho ETL (Kettle/Kitchen)

- ZMQ/Push notifications
- ReactPHP/PHP-PM

Some features
=============

- Uses an extended version of the Propel schema which allows the definition of extended attributes (lookups, triggers, default controls, views...)
- Generates and maintains additional SQL/Php files to ease the management of complex lookups and behaviours while maintaining referential integrity
- The database schema is extensible, which means that new fields can be defined with UI tools, then used in forms and lists as if they were physical fields. Uses Postgres JSON containers
- Multi tenancy (one schema file, multiple actual schemas configurable at runtime). Facilities to aggregate results from multiple schemas
- Automatic schema structure synchronization (via ApgDiff)
- Fully configurable database level auditing (generates and maintains audit schemas that can capture any modification done to the master records, even for files)
- File attachments to records, managed with a database index and file system (or cloud) storage
- Provides tools to build lists and forms which are rendered in the UI with Dojo/Dijit/Gridx, and are fully configurable by the user without code (all configurations remain in the database)
- In-database translations
- Provides Pentaho Kettle plugins to interact with the system via its REST interface
- Can render Activiti Workflows as if they were native wizards in the application

License
=======

MIT, see the attached LICENSE file

  