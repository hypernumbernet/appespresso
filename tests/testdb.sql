-- テスト用DB作成SQL
-- PostgreSQL

CREATE ROLE test LOGIN ENCRYPTED PASSWORD 'md5d4368e4816f0cb8202bb7dc136e2e99d'
   VALID UNTIL 'infinity';

CREATE DATABASE appespresso_test
  WITH OWNER = test
       ENCODING = 'UTF8'
       TABLESPACE = pg_default
       LC_COLLATE = 'C'
       LC_CTYPE = 'C'
       CONNECTION LIMIT = -1;

CREATE TABLE public.test
(
  id integer NOT NULL DEFAULT nextval('test_id_seq'::regclass),
  str1 text,
  int1 integer,
  time1 timestamp without time zone,
  CONSTRAINT test_pkey PRIMARY KEY (id)
)
WITH (
  OIDS=FALSE
);
ALTER TABLE public.test
  OWNER TO test;
