DROP TABLE IF EXISTS lookups._date CASCADE;
CREATE TABLE lookups._date (
    _tech_id integer NOT NULL,
    date date,
    year INTEGER,
    month INTEGER,
    month_name text,
    day INTEGER,
    day_of_year INTEGER,
    weekday_name text,
    calendar_week INTEGER,
    formatted_date text,
    quartal text,
    year_quartal text,
    year_month text,
    year_calendar_week text,
    weekend boolean,
    holiday_us boolean,
    holiday_it boolean,
    holiday_es boolean,
    holiday_pt boolean,
    period text,
    cw_start date,
    cw_end date,
    month_start date,
    month_end timestamp without time zone
);

ALTER TABLE lookups._date ADD PRIMARY KEY (_tech_id);
CREATE INDEX _date_pk_idx ON lookups._date USING btree (_tech_id);
CREATE INDEX _date_date_idx ON lookups._date USING btree ("date");
CREATE INDEX _date_year_idx ON lookups._date USING btree ("year");

DROP TABLE IF EXISTS lookups._time CASCADE;
CREATE TABLE lookups._time (
    _tech_id integer NOT NULL,
    time_of_day text,
    hour integer,
    quarter_hour text,
    minute integer,
    day_time_name text,
    day_night text
);
ALTER TABLE lookups._time ADD PRIMARY KEY (_tech_id);

INSERT INTO lookups._date
SELECT
    progressive_counter AS _tech_id,
	datum AS DATE,
	EXTRACT(YEAR FROM datum) AS YEAR,
	EXTRACT(MONTH FROM datum) AS MONTH,
	-- Localized month name
	to_char(datum, 'TMMonth') AS month_name,
	EXTRACT(DAY FROM datum) AS DAY,
	EXTRACT(doy FROM datum) AS day_of_year,
	-- Localized weekday
	to_char(datum, 'TMDay') AS weekday_name,
	-- ISO calendar week
	EXTRACT(week FROM datum) AS calendar_week,
	to_char(datum, 'dd/mm/yyyy') AS formatted_Date,
	'Q' || to_char(datum, 'Q') AS quartal,
	to_char(datum, 'yyyy/"Q"Q') AS year_quartal,
	to_char(datum, 'yyyy/mm') AS year_month,
	-- ISO calendar year and week
	to_char(datum, 'iyyy/IW') AS year_calendar_week,
	-- Weekend
	EXTRACT(isodow FROM datum) IN (6, 7)  AS weekend,
	-- Fixed holidays 
    to_char(datum, 'MMDD') IN ('0101', '0704', '1225', '1226') AS holiday_us,
	to_char(datum, 'MMDD') IN ('0101', '0106', '0501', '0815', '1101', '1208', '1225', '1226') AS holiday_it,
	to_char(datum, 'MMDD') IN ('0101', '0106', '0501', '0815', '1101', '1208', '1225', '1226') AS holiday_es,
	to_char(datum, 'MMDD') IN ('0101', '0106', '0501', '0815', '1101', '1208', '1225', '1226') AS holiday_pt,

    -- Some periods of the year, adjust for your organisation and country
	CASE WHEN to_char(datum, 'MMDD') BETWEEN '0701' AND '0831' THEN 'Summer break'
	     WHEN to_char(datum, 'MMDD') BETWEEN '1115' AND '1225' THEN 'Christmas season'
	     WHEN to_char(datum, 'MMDD') > '1225' OR to_char(datum, 'MMDD') <= '0106' THEN 'Winter break'
		ELSE 'Normal' END
		AS period,

	-- ISO start and end of the week of this date
	datum + (1 - EXTRACT(isodow FROM datum))::INTEGER AS cw_start,
	datum + (7 - EXTRACT(isodow FROM datum))::INTEGER AS cw_end,

	-- Start and end of the month of this date
	datum + (1 - EXTRACT(DAY FROM datum))::INTEGER AS month_start,
	(datum + (1 - EXTRACT(DAY FROM datum))::INTEGER + '1 month'::INTERVAL)::DATE - '1 day'::INTERVAL AS month_end
FROM (
	-- 1 leap year every 4 years
	SELECT 
        1+SEQUENCE.DAY AS progressive_counter,
        '1900-01-01'::DATE + SEQUENCE.DAY AS datum
	FROM generate_series(0,365*200) AS SEQUENCE(DAY)
	GROUP BY SEQUENCE.DAY
     ) DQ
ORDER BY 1;




INSERT INTO lookups._time
SELECT 
    progressive_counter AS _tech_id,
    to_char(MINUTE, 'hh24:mi') AS time_of_day,
	-- Hour of the day (0 - 23)
	EXTRACT(HOUR FROM MINUTE) AS HOUR, 
	-- Extract and format quarter hours
	to_char(MINUTE - (EXTRACT(MINUTE FROM MINUTE)::INTEGER % 15 || 'minutes')::INTERVAL, 'hh24:mi') ||
	' – ' ||
	to_char(MINUTE - (EXTRACT(MINUTE FROM MINUTE)::INTEGER % 15 || 'minutes')::INTERVAL + '14 minutes'::INTERVAL, 'hh24:mi')
		AS quarter_hour,
	-- Minute of the day (0 - 1439)
	EXTRACT(HOUR FROM MINUTE)*60 + EXTRACT(MINUTE FROM MINUTE) AS MINUTE,
	-- Names of day periods
	CASE WHEN to_char(MINUTE, 'hh24:mi') BETWEEN '06:00' AND '08:29'
		THEN 'Morning'
	     WHEN to_char(MINUTE, 'hh24:mi') BETWEEN '08:30' AND '11:59'
		THEN 'AM'
	     WHEN to_char(MINUTE, 'hh24:mi') BETWEEN '12:00' AND '17:59'
		THEN 'PM'
	     WHEN to_char(MINUTE, 'hh24:mi') BETWEEN '18:00' AND '22:29'
		THEN 'Evening'
	     ELSE 'Night'
	END AS day_time_name,
	-- Indicator of day or night
	CASE WHEN to_char(MINUTE, 'hh24:mi') BETWEEN '07:00' AND '19:59' THEN 'Day'
	     ELSE 'Night'
	END AS day_night
FROM (
    SELECT 
        1+SEQUENCE.MINUTE AS progressive_counter,
        '0:00'::TIME + (SEQUENCE.MINUTE || ' minutes')::INTERVAL AS MINUTE
	FROM generate_series(0,1439) AS SEQUENCE(MINUTE)
	GROUP BY SEQUENCE.MINUTE
     ) DQ
ORDER BY 1;