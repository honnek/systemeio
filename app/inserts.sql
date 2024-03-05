--
-- PostgreSQL database dump
--

-- Dumped from database version 16.2 (Debian 16.2-1.pgdg120+2)
-- Dumped by pg_dump version 16.2 (Debian 16.2-1.pgdg120+2)

SET statement_timeout = 0;
SET lock_timeout = 0;
SET idle_in_transaction_session_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SELECT pg_catalog.set_config('search_path', '', false);
SET check_function_bodies = false;
SET xmloption = content;
SET client_min_messages = warning;
SET row_security = off;

--
-- Data for Name: coupon; Type: TABLE DATA; Schema: public; Owner: postgres
--

INSERT INTO public.coupon (id, type, code, value) VALUES (1, 'PERCENT', 'P10', 10.00);
INSERT INTO public.coupon (id, type, code, value) VALUES (2, 'PERCENT', 'P90', 90.00);
INSERT INTO public.coupon (id, type, code, value) VALUES (3, 'FIXED', 'F10', 10.00);
INSERT INTO public.coupon (id, type, code, value) VALUES (4, 'FIXED', 'F90', 90.00);


--
-- Data for Name: doctrine_migration_versions; Type: TABLE DATA; Schema: public; Owner: postgres
--

INSERT INTO public.doctrine_migration_versions (version, executed_at, execution_time) VALUES ('DoctrineMigrations\Version20240229082737', '2024-02-29 10:34:14', 3);
INSERT INTO public.doctrine_migration_versions (version, executed_at, execution_time) VALUES ('DoctrineMigrations\Version20240229083315', '2024-02-29 10:34:14', 5);
INSERT INTO public.doctrine_migration_versions (version, executed_at, execution_time) VALUES ('DoctrineMigrations\Version20240229083951', '2024-02-29 10:40:23', 39);
INSERT INTO public.doctrine_migration_versions (version, executed_at, execution_time) VALUES ('DoctrineMigrations\Version20240229085456', '2024-02-29 10:55:46', 51);
INSERT INTO public.doctrine_migration_versions (version, executed_at, execution_time) VALUES ('DoctrineMigrations\Version20240229095435', '2024-02-29 11:54:44', 2);
INSERT INTO public.doctrine_migration_versions (version, executed_at, execution_time) VALUES ('DoctrineMigrations\Version20240229101737', '2024-02-29 12:18:03', 6);
INSERT INTO public.doctrine_migration_versions (version, executed_at, execution_time) VALUES ('DoctrineMigrations\Version20240302144936', '2024-03-02 16:56:02', 42);
INSERT INTO public.doctrine_migration_versions (version, executed_at, execution_time) VALUES ('DoctrineMigrations\Version20240303174536', '2024-03-03 19:46:43', 4);


--
-- Data for Name: product; Type: TABLE DATA; Schema: public; Owner: postgres
--

INSERT INTO public.product (id, title, price, currency) VALUES (1, 'Iphone', 100.00, 'EURO');
INSERT INTO public.product (id, title, price, currency) VALUES (2, 'Наушники', 20.00, 'EURO');
INSERT INTO public.product (id, title, price, currency) VALUES (3, 'Чехол', 10.00, 'EURO');


--
-- Data for Name: purchase; Type: TABLE DATA; Schema: public; Owner: postgres
--



--
-- Name: coupon_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.coupon_id_seq', 1, false);


--
-- Name: product_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.product_id_seq', 1, false);


--
-- Name: purchase_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.purchase_id_seq', 12, true);


--
-- PostgreSQL database dump complete
--

