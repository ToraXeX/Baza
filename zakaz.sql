--
-- PostgreSQL database dump
--

-- Dumped from database version 16.3
-- Dumped by pg_dump version 16.3

-- Started on 2024-06-10 03:02:40

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
-- TOC entry 2 (class 3079 OID 24789)
-- Name: uuid-ossp; Type: EXTENSION; Schema: -; Owner: -
--

CREATE EXTENSION IF NOT EXISTS "uuid-ossp" WITH SCHEMA public;


--
-- TOC entry 4983 (class 0 OID 0)
-- Dependencies: 2
-- Name: EXTENSION "uuid-ossp"; Type: COMMENT; Schema: -; Owner: 
--

COMMENT ON EXTENSION "uuid-ossp" IS 'generate universally unique identifiers (UUIDs)';


--
-- TOC entry 237 (class 1255 OID 24747)
-- Name: update_delivery_cost(); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION public.update_delivery_cost() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
DECLARE
    total_quantity INTEGER;
BEGIN
    -- Вычисляем суммарное количество товаров в заказе
    SELECT SUM(qua_kolvo) INTO total_quantity
    FROM prodtozak
    WHERE zak_id = NEW.zak_id;

    -- Проверяем условие (больше 60 товаров)
    IF total_quantity > 60 THEN
        -- Обновляем цену доставки на 20% в соответствующей записи в таблице delivery
        UPDATE delivery
        SET del_cost = del_cost * 1.03
        WHERE zak_id = NEW.zak_id;
    END IF;

    RETURN NEW;
END;
$$;


ALTER FUNCTION public.update_delivery_cost() OWNER TO postgres;

--
-- TOC entry 259 (class 1255 OID 24754)
-- Name: update_delivery_date(character varying, integer, date); Type: PROCEDURE; Schema: public; Owner: postgres
--

CREATE PROCEDURE public.update_delivery_date(IN p_cus_phone character varying, IN p_zak_id integer, IN p_new_delivery_date date)
    LANGUAGE plpgsql
    AS $$
DECLARE
    v_customer_id INTEGER;
BEGIN
    -- Находим идентификатор заказчика по номеру телефона
    SELECT cus_id INTO v_customer_id
    FROM customer
    WHERE cus_phone = p_cus_phone;

    -- Проверка наличия заказчика
    IF v_customer_id IS NULL THEN
        RAISE EXCEPTION 'Заказчик с указанным номером телефона не найден.';
    END IF;

    -- Проверка наличия заказа с указанным идентификатором
    IF NOT EXISTS (SELECT 1 FROM zakaz WHERE zak_id = p_zak_id) THEN
        RAISE EXCEPTION 'Заказ с указанным идентификатором не найден.';
    END IF;

    -- Обновление даты доставки в таблице delivery
    UPDATE delivery
    SET del_date = p_new_delivery_date
    WHERE zak_id = p_zak_id;

    -- Обновление даты доставки в таблице zakaz
    UPDATE zakaz
    SET zak_due_date = p_new_delivery_date
    WHERE zak_id = p_zak_id;
END;
$$;


ALTER PROCEDURE public.update_delivery_date(IN p_cus_phone character varying, IN p_zak_id integer, IN p_new_delivery_date date) OWNER TO postgres;

--
-- TOC entry 236 (class 1255 OID 24745)
-- Name: update_zakaz_status(); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION public.update_zakaz_status() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
BEGIN
    -- Проверка наличия активных (в процессе выполнения) доставок для данного заказа
    IF EXISTS (
        SELECT 1
        FROM delivery d
        WHERE d.zak_id = NEW.zak_id AND d.del_status = 'в процессе выполнения'
    ) THEN
        UPDATE zakaz
        SET zak_status = '1' -- Установка статуса "выполняется"
        WHERE zak_id = NEW.zak_id;

    -- Проверка завершения всех доставок для данного заказа
    ELSIF NOT EXISTS (
        SELECT 1
        FROM delivery d
        WHERE d.zak_id = NEW.zak_id AND d.del_status = 'выполнен'
    ) THEN
        UPDATE zakaz
        SET zak_status = '0' -- Установка статуса "отменен"
        WHERE zak_id = NEW.zak_id;

    -- Все доставки выполнены
    ELSE
        UPDATE zakaz
        SET zak_status = '3' -- Установка статуса "выполнен"
        WHERE zak_id = NEW.zak_id;
    END IF;

    RETURN NEW;
END;
$$;


ALTER FUNCTION public.update_zakaz_status() OWNER TO postgres;

SET default_tablespace = '';

SET default_table_access_method = heap;

--
-- TOC entry 234 (class 1259 OID 24763)
-- Name: cart; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.cart (
    cart_id integer NOT NULL,
    customer_id integer,
    product_id integer,
    quantity integer
);


ALTER TABLE public.cart OWNER TO postgres;

--
-- TOC entry 233 (class 1259 OID 24762)
-- Name: cart_cart_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.cart_cart_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.cart_cart_id_seq OWNER TO postgres;

--
-- TOC entry 4984 (class 0 OID 0)
-- Dependencies: 233
-- Name: cart_cart_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.cart_cart_id_seq OWNED BY public.cart.cart_id;


--
-- TOC entry 217 (class 1259 OID 24577)
-- Name: contract; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.contract (
    con_id integer NOT NULL,
    emp_id integer NOT NULL,
    con_date date NOT NULL,
    con_number character varying(16) NOT NULL,
    cus_id integer
);


ALTER TABLE public.contract OWNER TO postgres;

--
-- TOC entry 216 (class 1259 OID 24576)
-- Name: contract_con_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.contract_con_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.contract_con_id_seq OWNER TO postgres;

--
-- TOC entry 4985 (class 0 OID 0)
-- Dependencies: 216
-- Name: contract_con_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.contract_con_id_seq OWNED BY public.contract.con_id;


--
-- TOC entry 232 (class 1259 OID 24737)
-- Name: customer_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.customer_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.customer_id_seq OWNER TO postgres;

--
-- TOC entry 229 (class 1259 OID 24639)
-- Name: customer; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.customer (
    cus_name character varying(64),
    cus_phone character(10),
    cus_id integer DEFAULT nextval('public.customer_id_seq'::regclass) NOT NULL
);


ALTER TABLE public.customer OWNER TO postgres;

--
-- TOC entry 219 (class 1259 OID 24587)
-- Name: delivery; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.delivery (
    del_id integer NOT NULL,
    zak_id integer NOT NULL,
    del_cost integer NOT NULL,
    del_date date NOT NULL,
    del_status "char"
);


ALTER TABLE public.delivery OWNER TO postgres;

--
-- TOC entry 218 (class 1259 OID 24586)
-- Name: delivery_del_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.delivery_del_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.delivery_del_id_seq OWNER TO postgres;

--
-- TOC entry 4986 (class 0 OID 0)
-- Dependencies: 218
-- Name: delivery_del_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.delivery_del_id_seq OWNED BY public.delivery.del_id;


--
-- TOC entry 220 (class 1259 OID 24595)
-- Name: doing; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.doing (
    del_id integer NOT NULL,
    emp_id integer NOT NULL
);


ALTER TABLE public.doing OWNER TO postgres;

--
-- TOC entry 222 (class 1259 OID 24604)
-- Name: employee; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.employee (
    emp_id integer NOT NULL,
    emp_name character varying(64) NOT NULL,
    emp_position character varying(32) NOT NULL
);


ALTER TABLE public.employee OWNER TO postgres;

--
-- TOC entry 221 (class 1259 OID 24603)
-- Name: employee_emp_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.employee_emp_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.employee_emp_id_seq OWNER TO postgres;

--
-- TOC entry 4987 (class 0 OID 0)
-- Dependencies: 221
-- Name: employee_emp_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.employee_emp_id_seq OWNED BY public.employee.emp_id;


--
-- TOC entry 231 (class 1259 OID 24653)
-- Name: prodtozak; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.prodtozak (
    zak_id integer NOT NULL,
    prd_id integer NOT NULL,
    qua_kolvo integer NOT NULL
);


ALTER TABLE public.prodtozak OWNER TO postgres;

--
-- TOC entry 224 (class 1259 OID 24612)
-- Name: products; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.products (
    prd_id integer NOT NULL,
    qua_kolvo integer NOT NULL,
    prd_desc text NOT NULL,
    prd_value integer
);


ALTER TABLE public.products OWNER TO postgres;

--
-- TOC entry 223 (class 1259 OID 24611)
-- Name: products_prd_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.products_prd_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.products_prd_id_seq OWNER TO postgres;

--
-- TOC entry 4988 (class 0 OID 0)
-- Dependencies: 223
-- Name: products_prd_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.products_prd_id_seq OWNED BY public.products.prd_id;


--
-- TOC entry 235 (class 1259 OID 24800)
-- Name: user_passwords; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.user_passwords (
    cus_id uuid DEFAULT public.uuid_generate_v4() NOT NULL,
    cus_name character varying(255) NOT NULL,
    password_hash character varying(255) NOT NULL,
    role character varying(20)
);


ALTER TABLE public.user_passwords OWNER TO postgres;

--
-- TOC entry 226 (class 1259 OID 24622)
-- Name: warehouse; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.warehouse (
    whs_id integer NOT NULL,
    whs_address character varying(64) NOT NULL,
    whs_name character varying(32) NOT NULL
);


ALTER TABLE public.warehouse OWNER TO postgres;

--
-- TOC entry 225 (class 1259 OID 24621)
-- Name: warehouse_whs_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.warehouse_whs_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.warehouse_whs_id_seq OWNER TO postgres;

--
-- TOC entry 4989 (class 0 OID 0)
-- Dependencies: 225
-- Name: warehouse_whs_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.warehouse_whs_id_seq OWNED BY public.warehouse.whs_id;


--
-- TOC entry 230 (class 1259 OID 24645)
-- Name: wartoprod; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.wartoprod (
    whs_id integer NOT NULL,
    prd_id integer NOT NULL,
    qua_kolvo integer NOT NULL
);


ALTER TABLE public.wartoprod OWNER TO postgres;

--
-- TOC entry 228 (class 1259 OID 24630)
-- Name: zakaz; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.zakaz (
    zak_id integer NOT NULL,
    con_id integer NOT NULL,
    zak_date date NOT NULL,
    zak_prep_date date NOT NULL,
    zak_status character varying(32) NOT NULL,
    zak_due_date date NOT NULL
);


ALTER TABLE public.zakaz OWNER TO postgres;

--
-- TOC entry 4990 (class 0 OID 0)
-- Dependencies: 228
-- Name: COLUMN zakaz.zak_due_date; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.zakaz.zak_due_date IS '0 -      
1 -       
2 -            
3 -         ';


--
-- TOC entry 227 (class 1259 OID 24629)
-- Name: zakaz_zak_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.zakaz_zak_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.zakaz_zak_id_seq OWNER TO postgres;

--
-- TOC entry 4991 (class 0 OID 0)
-- Dependencies: 227
-- Name: zakaz_zak_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.zakaz_zak_id_seq OWNED BY public.zakaz.zak_id;


--
-- TOC entry 4760 (class 2604 OID 24766)
-- Name: cart cart_id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.cart ALTER COLUMN cart_id SET DEFAULT nextval('public.cart_cart_id_seq'::regclass);


--
-- TOC entry 4753 (class 2604 OID 24580)
-- Name: contract con_id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.contract ALTER COLUMN con_id SET DEFAULT nextval('public.contract_con_id_seq'::regclass);


--
-- TOC entry 4754 (class 2604 OID 24590)
-- Name: delivery del_id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.delivery ALTER COLUMN del_id SET DEFAULT nextval('public.delivery_del_id_seq'::regclass);


--
-- TOC entry 4755 (class 2604 OID 24607)
-- Name: employee emp_id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.employee ALTER COLUMN emp_id SET DEFAULT nextval('public.employee_emp_id_seq'::regclass);


--
-- TOC entry 4756 (class 2604 OID 24615)
-- Name: products prd_id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.products ALTER COLUMN prd_id SET DEFAULT nextval('public.products_prd_id_seq'::regclass);


--
-- TOC entry 4757 (class 2604 OID 24625)
-- Name: warehouse whs_id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.warehouse ALTER COLUMN whs_id SET DEFAULT nextval('public.warehouse_whs_id_seq'::regclass);


--
-- TOC entry 4758 (class 2604 OID 24633)
-- Name: zakaz zak_id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.zakaz ALTER COLUMN zak_id SET DEFAULT nextval('public.zakaz_zak_id_seq'::regclass);


--
-- TOC entry 4976 (class 0 OID 24763)
-- Dependencies: 234
-- Data for Name: cart; Type: TABLE DATA; Schema: public; Owner: postgres
--

INSERT INTO public.cart (cart_id, customer_id, product_id, quantity) VALUES (33, 1, 13, 1);
INSERT INTO public.cart (cart_id, customer_id, product_id, quantity) VALUES (34, 1, 12, 1);


--
-- TOC entry 4959 (class 0 OID 24577)
-- Dependencies: 217
-- Data for Name: contract; Type: TABLE DATA; Schema: public; Owner: postgres
--

INSERT INTO public.contract (con_id, emp_id, con_date, con_number, cus_id) VALUES (2, 100, '2023-11-01', '№327', 10);
INSERT INTO public.contract (con_id, emp_id, con_date, con_number, cus_id) VALUES (3, 100, '2023-10-01', '№405', 40);
INSERT INTO public.contract (con_id, emp_id, con_date, con_number, cus_id) VALUES (4, 100, '2022-06-01', '№470', 40);
INSERT INTO public.contract (con_id, emp_id, con_date, con_number, cus_id) VALUES (5, 200, '2022-08-01', '№700', 20);
INSERT INTO public.contract (con_id, emp_id, con_date, con_number, cus_id) VALUES (6, 200, '2021-01-06', '№1', 20);
INSERT INTO public.contract (con_id, emp_id, con_date, con_number, cus_id) VALUES (7, 200, '2020-01-06', '№795', 30);
INSERT INTO public.contract (con_id, emp_id, con_date, con_number, cus_id) VALUES (8, 300, '2020-01-20', '№30', 10);
INSERT INTO public.contract (con_id, emp_id, con_date, con_number, cus_id) VALUES (9, 300, '2020-01-24', '№670
', 10);
INSERT INTO public.contract (con_id, emp_id, con_date, con_number, cus_id) VALUES (1, 200, '2023-12-02', '№329', 20);
INSERT INTO public.contract (con_id, emp_id, con_date, con_number, cus_id) VALUES (10, 200, '2023-10-01', '№405', 40);


--
-- TOC entry 4971 (class 0 OID 24639)
-- Dependencies: 229
-- Data for Name: customer; Type: TABLE DATA; Schema: public; Owner: postgres
--

INSERT INTO public.customer (cus_name, cus_phone, cus_id) VALUES ('Кибер-Тора', '9005987511', 20);
INSERT INTO public.customer (cus_name, cus_phone, cus_id) VALUES ('Кибер-Никита', '9005987522', 30);
INSERT INTO public.customer (cus_name, cus_phone, cus_id) VALUES ('Кибер-Иван', '9005987544', 40);
INSERT INTO public.customer (cus_name, cus_phone, cus_id) VALUES ('Кибер-Макс', '9005987510', 50);
INSERT INTO public.customer (cus_name, cus_phone, cus_id) VALUES ('Дед-Мороз', '********* ', 10);


--
-- TOC entry 4961 (class 0 OID 24587)
-- Dependencies: 219
-- Data for Name: delivery; Type: TABLE DATA; Schema: public; Owner: postgres
--

INSERT INTO public.delivery (del_id, zak_id, del_cost, del_date, del_status) VALUES (1, 1, 658, '2024-01-05', '3');
INSERT INTO public.delivery (del_id, zak_id, del_cost, del_date, del_status) VALUES (10, 1, 1313, '2024-01-05', '3');
INSERT INTO public.delivery (del_id, zak_id, del_cost, del_date, del_status) VALUES (8, 8, 800, '2024-01-07', '3');
INSERT INTO public.delivery (del_id, zak_id, del_cost, del_date, del_status) VALUES (2, 2, 500, '2023-11-07', '3');
INSERT INTO public.delivery (del_id, zak_id, del_cost, del_date, del_status) VALUES (3, 3, 660, '2023-10-07', '0');
INSERT INTO public.delivery (del_id, zak_id, del_cost, del_date, del_status) VALUES (5, 5, 518, '2022-08-07', '1');
INSERT INTO public.delivery (del_id, zak_id, del_cost, del_date, del_status) VALUES (6, 6, 570, '2021-12-07', '3');
INSERT INTO public.delivery (del_id, zak_id, del_cost, del_date, del_status) VALUES (9, 9, 770, '2020-01-31', '3');
INSERT INTO public.delivery (del_id, zak_id, del_cost, del_date, del_status) VALUES (11, 2, 1200, '2023-11-07', '3');
INSERT INTO public.delivery (del_id, zak_id, del_cost, del_date, del_status) VALUES (12, 3, 880, '2023-10-14', '3');
INSERT INTO public.delivery (del_id, zak_id, del_cost, del_date, del_status) VALUES (7, 7, 658, '2021-01-14', '3');
INSERT INTO public.delivery (del_id, zak_id, del_cost, del_date, del_status) VALUES (4, 4, 788, '2022-06-07', '1');
INSERT INTO public.delivery (del_id, zak_id, del_cost, del_date, del_status) VALUES (13, 4, 453, '2022-06-14', '3');


--
-- TOC entry 4962 (class 0 OID 24595)
-- Dependencies: 220
-- Data for Name: doing; Type: TABLE DATA; Schema: public; Owner: postgres
--

INSERT INTO public.doing (del_id, emp_id) VALUES (13, 500);
INSERT INTO public.doing (del_id, emp_id) VALUES (12, 600);
INSERT INTO public.doing (del_id, emp_id) VALUES (11, 600);
INSERT INTO public.doing (del_id, emp_id) VALUES (10, 500);
INSERT INTO public.doing (del_id, emp_id) VALUES (9, 600);
INSERT INTO public.doing (del_id, emp_id) VALUES (8, 500);
INSERT INTO public.doing (del_id, emp_id) VALUES (7, 500);
INSERT INTO public.doing (del_id, emp_id) VALUES (6, 600);
INSERT INTO public.doing (del_id, emp_id) VALUES (5, 500);
INSERT INTO public.doing (del_id, emp_id) VALUES (4, 600);
INSERT INTO public.doing (del_id, emp_id) VALUES (3, 600);
INSERT INTO public.doing (del_id, emp_id) VALUES (2, 500);
INSERT INTO public.doing (del_id, emp_id) VALUES (1, 500);


--
-- TOC entry 4964 (class 0 OID 24604)
-- Dependencies: 222
-- Data for Name: employee; Type: TABLE DATA; Schema: public; Owner: postgres
--

INSERT INTO public.employee (emp_id, emp_name, emp_position) VALUES (300, 'Андрей', 'Менеджер');
INSERT INTO public.employee (emp_id, emp_name, emp_position) VALUES (200, 'Денис', 'Менеджер');
INSERT INTO public.employee (emp_id, emp_name, emp_position) VALUES (100, 'Тора', 'Менеджер');
INSERT INTO public.employee (emp_id, emp_name, emp_position) VALUES (600, 'Иван', 'Курьер');
INSERT INTO public.employee (emp_id, emp_name, emp_position) VALUES (500, 'Алена', 'Менеджер');


--
-- TOC entry 4973 (class 0 OID 24653)
-- Dependencies: 231
-- Data for Name: prodtozak; Type: TABLE DATA; Schema: public; Owner: postgres
--

INSERT INTO public.prodtozak (zak_id, prd_id, qua_kolvo) VALUES (13, 11, 5);
INSERT INTO public.prodtozak (zak_id, prd_id, qua_kolvo) VALUES (13, 10, 5);
INSERT INTO public.prodtozak (zak_id, prd_id, qua_kolvo) VALUES (13, 9, 5);
INSERT INTO public.prodtozak (zak_id, prd_id, qua_kolvo) VALUES (13, 8, 5);
INSERT INTO public.prodtozak (zak_id, prd_id, qua_kolvo) VALUES (13, 7, 5);
INSERT INTO public.prodtozak (zak_id, prd_id, qua_kolvo) VALUES (12, 20, 1);
INSERT INTO public.prodtozak (zak_id, prd_id, qua_kolvo) VALUES (12, 19, 2);
INSERT INTO public.prodtozak (zak_id, prd_id, qua_kolvo) VALUES (12, 18, 25);
INSERT INTO public.prodtozak (zak_id, prd_id, qua_kolvo) VALUES (12, 17, 5);
INSERT INTO public.prodtozak (zak_id, prd_id, qua_kolvo) VALUES (12, 16, 5);
INSERT INTO public.prodtozak (zak_id, prd_id, qua_kolvo) VALUES (12, 15, 4);
INSERT INTO public.prodtozak (zak_id, prd_id, qua_kolvo) VALUES (12, 14, 4);
INSERT INTO public.prodtozak (zak_id, prd_id, qua_kolvo) VALUES (12, 13, 2);
INSERT INTO public.prodtozak (zak_id, prd_id, qua_kolvo) VALUES (12, 12, 11);
INSERT INTO public.prodtozak (zak_id, prd_id, qua_kolvo) VALUES (11, 11, 20);
INSERT INTO public.prodtozak (zak_id, prd_id, qua_kolvo) VALUES (11, 10, 25);
INSERT INTO public.prodtozak (zak_id, prd_id, qua_kolvo) VALUES (11, 9, 5);
INSERT INTO public.prodtozak (zak_id, prd_id, qua_kolvo) VALUES (11, 8, 4);
INSERT INTO public.prodtozak (zak_id, prd_id, qua_kolvo) VALUES (11, 7, 2);
INSERT INTO public.prodtozak (zak_id, prd_id, qua_kolvo) VALUES (9, 20, 1);
INSERT INTO public.prodtozak (zak_id, prd_id, qua_kolvo) VALUES (9, 19, 2);
INSERT INTO public.prodtozak (zak_id, prd_id, qua_kolvo) VALUES (9, 18, 25);
INSERT INTO public.prodtozak (zak_id, prd_id, qua_kolvo) VALUES (9, 17, 5);
INSERT INTO public.prodtozak (zak_id, prd_id, qua_kolvo) VALUES (9, 16, 5);
INSERT INTO public.prodtozak (zak_id, prd_id, qua_kolvo) VALUES (9, 15, 4);
INSERT INTO public.prodtozak (zak_id, prd_id, qua_kolvo) VALUES (9, 14, 4);
INSERT INTO public.prodtozak (zak_id, prd_id, qua_kolvo) VALUES (9, 13, 2);
INSERT INTO public.prodtozak (zak_id, prd_id, qua_kolvo) VALUES (9, 12, 11);
INSERT INTO public.prodtozak (zak_id, prd_id, qua_kolvo) VALUES (8, 11, 20);
INSERT INTO public.prodtozak (zak_id, prd_id, qua_kolvo) VALUES (8, 10, 25);
INSERT INTO public.prodtozak (zak_id, prd_id, qua_kolvo) VALUES (8, 9, 5);
INSERT INTO public.prodtozak (zak_id, prd_id, qua_kolvo) VALUES (8, 8, 4);
INSERT INTO public.prodtozak (zak_id, prd_id, qua_kolvo) VALUES (8, 7, 2);
INSERT INTO public.prodtozak (zak_id, prd_id, qua_kolvo) VALUES (6, 20, 1);
INSERT INTO public.prodtozak (zak_id, prd_id, qua_kolvo) VALUES (6, 19, 2);
INSERT INTO public.prodtozak (zak_id, prd_id, qua_kolvo) VALUES (6, 18, 25);
INSERT INTO public.prodtozak (zak_id, prd_id, qua_kolvo) VALUES (6, 17, 5);
INSERT INTO public.prodtozak (zak_id, prd_id, qua_kolvo) VALUES (6, 16, 5);
INSERT INTO public.prodtozak (zak_id, prd_id, qua_kolvo) VALUES (6, 15, 4);
INSERT INTO public.prodtozak (zak_id, prd_id, qua_kolvo) VALUES (6, 14, 4);
INSERT INTO public.prodtozak (zak_id, prd_id, qua_kolvo) VALUES (6, 13, 2);
INSERT INTO public.prodtozak (zak_id, prd_id, qua_kolvo) VALUES (6, 12, 11);
INSERT INTO public.prodtozak (zak_id, prd_id, qua_kolvo) VALUES (5, 11, 20);
INSERT INTO public.prodtozak (zak_id, prd_id, qua_kolvo) VALUES (5, 10, 25);
INSERT INTO public.prodtozak (zak_id, prd_id, qua_kolvo) VALUES (5, 9, 5);
INSERT INTO public.prodtozak (zak_id, prd_id, qua_kolvo) VALUES (5, 8, 4);
INSERT INTO public.prodtozak (zak_id, prd_id, qua_kolvo) VALUES (5, 7, 2);
INSERT INTO public.prodtozak (zak_id, prd_id, qua_kolvo) VALUES (3, 20, 1);
INSERT INTO public.prodtozak (zak_id, prd_id, qua_kolvo) VALUES (3, 19, 2);
INSERT INTO public.prodtozak (zak_id, prd_id, qua_kolvo) VALUES (3, 18, 25);
INSERT INTO public.prodtozak (zak_id, prd_id, qua_kolvo) VALUES (3, 17, 5);
INSERT INTO public.prodtozak (zak_id, prd_id, qua_kolvo) VALUES (3, 16, 5);
INSERT INTO public.prodtozak (zak_id, prd_id, qua_kolvo) VALUES (3, 15, 4);
INSERT INTO public.prodtozak (zak_id, prd_id, qua_kolvo) VALUES (3, 14, 4);
INSERT INTO public.prodtozak (zak_id, prd_id, qua_kolvo) VALUES (3, 13, 2);
INSERT INTO public.prodtozak (zak_id, prd_id, qua_kolvo) VALUES (3, 12, 11);
INSERT INTO public.prodtozak (zak_id, prd_id, qua_kolvo) VALUES (2, 11, 20);
INSERT INTO public.prodtozak (zak_id, prd_id, qua_kolvo) VALUES (2, 10, 25);
INSERT INTO public.prodtozak (zak_id, prd_id, qua_kolvo) VALUES (2, 9, 5);
INSERT INTO public.prodtozak (zak_id, prd_id, qua_kolvo) VALUES (2, 8, 4);
INSERT INTO public.prodtozak (zak_id, prd_id, qua_kolvo) VALUES (2, 7, 2);
INSERT INTO public.prodtozak (zak_id, prd_id, qua_kolvo) VALUES (10, 6, 4);
INSERT INTO public.prodtozak (zak_id, prd_id, qua_kolvo) VALUES (10, 5, 15);
INSERT INTO public.prodtozak (zak_id, prd_id, qua_kolvo) VALUES (10, 4, 10);
INSERT INTO public.prodtozak (zak_id, prd_id, qua_kolvo) VALUES (10, 3, 15);
INSERT INTO public.prodtozak (zak_id, prd_id, qua_kolvo) VALUES (10, 2, 10);
INSERT INTO public.prodtozak (zak_id, prd_id, qua_kolvo) VALUES (10, 1, 20);
INSERT INTO public.prodtozak (zak_id, prd_id, qua_kolvo) VALUES (7, 6, 4);
INSERT INTO public.prodtozak (zak_id, prd_id, qua_kolvo) VALUES (7, 5, 15);
INSERT INTO public.prodtozak (zak_id, prd_id, qua_kolvo) VALUES (7, 4, 10);
INSERT INTO public.prodtozak (zak_id, prd_id, qua_kolvo) VALUES (7, 3, 15);
INSERT INTO public.prodtozak (zak_id, prd_id, qua_kolvo) VALUES (7, 2, 10);
INSERT INTO public.prodtozak (zak_id, prd_id, qua_kolvo) VALUES (7, 1, 20);
INSERT INTO public.prodtozak (zak_id, prd_id, qua_kolvo) VALUES (4, 6, 4);
INSERT INTO public.prodtozak (zak_id, prd_id, qua_kolvo) VALUES (4, 5, 15);
INSERT INTO public.prodtozak (zak_id, prd_id, qua_kolvo) VALUES (4, 4, 10);
INSERT INTO public.prodtozak (zak_id, prd_id, qua_kolvo) VALUES (4, 3, 15);
INSERT INTO public.prodtozak (zak_id, prd_id, qua_kolvo) VALUES (4, 2, 10);
INSERT INTO public.prodtozak (zak_id, prd_id, qua_kolvo) VALUES (4, 1, 20);
INSERT INTO public.prodtozak (zak_id, prd_id, qua_kolvo) VALUES (1, 6, 4);
INSERT INTO public.prodtozak (zak_id, prd_id, qua_kolvo) VALUES (1, 5, 15);
INSERT INTO public.prodtozak (zak_id, prd_id, qua_kolvo) VALUES (1, 4, 10);
INSERT INTO public.prodtozak (zak_id, prd_id, qua_kolvo) VALUES (1, 3, 15);
INSERT INTO public.prodtozak (zak_id, prd_id, qua_kolvo) VALUES (1, 2, 10);
INSERT INTO public.prodtozak (zak_id, prd_id, qua_kolvo) VALUES (1, 1, 20);


--
-- TOC entry 4966 (class 0 OID 24612)
-- Dependencies: 224
-- Data for Name: products; Type: TABLE DATA; Schema: public; Owner: postgres
--

INSERT INTO public.products (prd_id, qua_kolvo, prd_desc, prd_value) VALUES (19, 200, 'Начос', 200);
INSERT INTO public.products (prd_id, qua_kolvo, prd_desc, prd_value) VALUES (18, 200, 'Лейс', 200);
INSERT INTO public.products (prd_id, qua_kolvo, prd_desc, prd_value) VALUES (17, 400, 'бп', 60);
INSERT INTO public.products (prd_id, qua_kolvo, prd_desc, prd_value) VALUES (16, 400, 'бп', 60);
INSERT INTO public.products (prd_id, qua_kolvo, prd_desc, prd_value) VALUES (15, 350, 'Скитлз', 100);
INSERT INTO public.products (prd_id, qua_kolvo, prd_desc, prd_value) VALUES (14, 350, 'Марс', 100);
INSERT INTO public.products (prd_id, qua_kolvo, prd_desc, prd_value) VALUES (13, 100, 'Твикс', 100);
INSERT INTO public.products (prd_id, qua_kolvo, prd_desc, prd_value) VALUES (12, 100, 'Баунти', 100);
INSERT INTO public.products (prd_id, qua_kolvo, prd_desc, prd_value) VALUES (11, 100, 'Редбул', 200);
INSERT INTO public.products (prd_id, qua_kolvo, prd_desc, prd_value) VALUES (10, 300, 'Адреналин', 190);
INSERT INTO public.products (prd_id, qua_kolvo, prd_desc, prd_value) VALUES (9, 300, 'Берн', 180);
INSERT INTO public.products (prd_id, qua_kolvo, prd_desc, prd_value) VALUES (8, 200, 'Торнадо', 170);
INSERT INTO public.products (prd_id, qua_kolvo, prd_desc, prd_value) VALUES (7, 150, 'Флеш', 160);
INSERT INTO public.products (prd_id, qua_kolvo, prd_desc, prd_value) VALUES (6, 150, 'Спрайт', 90);
INSERT INTO public.products (prd_id, qua_kolvo, prd_desc, prd_value) VALUES (5, 150, 'Фанта', 90);
INSERT INTO public.products (prd_id, qua_kolvo, prd_desc, prd_value) VALUES (4, 250, 'Кола', 100);
INSERT INTO public.products (prd_id, qua_kolvo, prd_desc, prd_value) VALUES (3, 250, 'Святой источник', 50);
INSERT INTO public.products (prd_id, qua_kolvo, prd_desc, prd_value) VALUES (2, 200, 'Актив', 80);
INSERT INTO public.products (prd_id, qua_kolvo, prd_desc, prd_value) VALUES (1, 200, 'Бон Аква', 70);
INSERT INTO public.products (prd_id, qua_kolvo, prd_desc, prd_value) VALUES (20, 200, 'Салат', 200);
INSERT INTO public.products (prd_id, qua_kolvo, prd_desc, prd_value) VALUES (21, 400, 'сникерс', 100);


--
-- TOC entry 4977 (class 0 OID 24800)
-- Dependencies: 235
-- Data for Name: user_passwords; Type: TABLE DATA; Schema: public; Owner: postgres
--

INSERT INTO public.user_passwords (cus_id, cus_name, password_hash, role) VALUES ('2bad4251-256f-4d83-9d57-e15f31fa1aae', 'toraa', '$2y$10$mW4uLrOePulVYpGJjPEBdeoYX0zScojpDxmiQFp1n28tQ/SApWdIS', NULL);


--
-- TOC entry 4968 (class 0 OID 24622)
-- Dependencies: 226
-- Data for Name: warehouse; Type: TABLE DATA; Schema: public; Owner: postgres
--

INSERT INTO public.warehouse (whs_id, whs_address, whs_name) VALUES (3, 'Площать победы 1', 'ВкусВилл');
INSERT INTO public.warehouse (whs_id, whs_address, whs_name) VALUES (2, 'Заводская площать 3', 'Самокат-Скла');
INSERT INTO public.warehouse (whs_id, whs_address, whs_name) VALUES (1, 'Генерала Мерулова 10А', 'Яндекс-Склад');


--
-- TOC entry 4972 (class 0 OID 24645)
-- Dependencies: 230
-- Data for Name: wartoprod; Type: TABLE DATA; Schema: public; Owner: postgres
--

INSERT INTO public.wartoprod (whs_id, prd_id, qua_kolvo) VALUES (3, 20, 4);
INSERT INTO public.wartoprod (whs_id, prd_id, qua_kolvo) VALUES (3, 19, 8);
INSERT INTO public.wartoprod (whs_id, prd_id, qua_kolvo) VALUES (3, 18, 100);
INSERT INTO public.wartoprod (whs_id, prd_id, qua_kolvo) VALUES (3, 17, 20);
INSERT INTO public.wartoprod (whs_id, prd_id, qua_kolvo) VALUES (3, 16, 20);
INSERT INTO public.wartoprod (whs_id, prd_id, qua_kolvo) VALUES (3, 15, 16);
INSERT INTO public.wartoprod (whs_id, prd_id, qua_kolvo) VALUES (3, 14, 16);
INSERT INTO public.wartoprod (whs_id, prd_id, qua_kolvo) VALUES (3, 13, 8);
INSERT INTO public.wartoprod (whs_id, prd_id, qua_kolvo) VALUES (3, 12, 44);
INSERT INTO public.wartoprod (whs_id, prd_id, qua_kolvo) VALUES (2, 11, 81);
INSERT INTO public.wartoprod (whs_id, prd_id, qua_kolvo) VALUES (2, 10, 101);
INSERT INTO public.wartoprod (whs_id, prd_id, qua_kolvo) VALUES (2, 9, 21);
INSERT INTO public.wartoprod (whs_id, prd_id, qua_kolvo) VALUES (2, 8, 17);
INSERT INTO public.wartoprod (whs_id, prd_id, qua_kolvo) VALUES (2, 7, 9);
INSERT INTO public.wartoprod (whs_id, prd_id, qua_kolvo) VALUES (1, 6, 17);
INSERT INTO public.wartoprod (whs_id, prd_id, qua_kolvo) VALUES (1, 5, 60);
INSERT INTO public.wartoprod (whs_id, prd_id, qua_kolvo) VALUES (1, 4, 40);
INSERT INTO public.wartoprod (whs_id, prd_id, qua_kolvo) VALUES (1, 3, 60);
INSERT INTO public.wartoprod (whs_id, prd_id, qua_kolvo) VALUES (1, 2, 40);
INSERT INTO public.wartoprod (whs_id, prd_id, qua_kolvo) VALUES (1, 1, 80);


--
-- TOC entry 4970 (class 0 OID 24630)
-- Dependencies: 228
-- Data for Name: zakaz; Type: TABLE DATA; Schema: public; Owner: postgres
--

INSERT INTO public.zakaz (zak_id, con_id, zak_date, zak_prep_date, zak_status, zak_due_date) VALUES (13, 4, '2022-06-07', '2023-06-08', '0', '2022-06-14');
INSERT INTO public.zakaz (zak_id, con_id, zak_date, zak_prep_date, zak_status, zak_due_date) VALUES (12, 3, '2023-10-07', '2023-10-08', '3', '2023-10-14');
INSERT INTO public.zakaz (zak_id, con_id, zak_date, zak_prep_date, zak_status, zak_due_date) VALUES (11, 2, '2023-11-01', '2023-11-08', '3', '2023-11-07');
INSERT INTO public.zakaz (zak_id, con_id, zak_date, zak_prep_date, zak_status, zak_due_date) VALUES (9, 9, '2020-01-24', '2020-01-25', '3', '2020-01-31');
INSERT INTO public.zakaz (zak_id, con_id, zak_date, zak_prep_date, zak_status, zak_due_date) VALUES (6, 6, '2021-12-01', '2021-12-02', '1', '2021-12-07');
INSERT INTO public.zakaz (zak_id, con_id, zak_date, zak_prep_date, zak_status, zak_due_date) VALUES (5, 5, '2022-08-01', '2022-08-02', '1', '2022-08-07');
INSERT INTO public.zakaz (zak_id, con_id, zak_date, zak_prep_date, zak_status, zak_due_date) VALUES (8, 8, '2020-01-20', '2021-12-21', '0', '2024-01-07');
INSERT INTO public.zakaz (zak_id, con_id, zak_date, zak_prep_date, zak_status, zak_due_date) VALUES (2, 2, '2023-11-01', '2023-11-02', '0', '2023-11-07');
INSERT INTO public.zakaz (zak_id, con_id, zak_date, zak_prep_date, zak_status, zak_due_date) VALUES (3, 3, '2023-10-01', '2023-10-02', '0', '2023-10-07');
INSERT INTO public.zakaz (zak_id, con_id, zak_date, zak_prep_date, zak_status, zak_due_date) VALUES (4, 4, '2022-06-01', '2023-06-02', '0', '2022-06-07');
INSERT INTO public.zakaz (zak_id, con_id, zak_date, zak_prep_date, zak_status, zak_due_date) VALUES (10, 1, '2023-12-01', '2023-12-08', '3', '2024-01-01');
INSERT INTO public.zakaz (zak_id, con_id, zak_date, zak_prep_date, zak_status, zak_due_date) VALUES (1, 1, '2023-12-01', '2023-12-02', '0', '2024-01-05');
INSERT INTO public.zakaz (zak_id, con_id, zak_date, zak_prep_date, zak_status, zak_due_date) VALUES (14, 10, '2024-01-24', '2024-01-24', '3', '2024-01-31');
INSERT INTO public.zakaz (zak_id, con_id, zak_date, zak_prep_date, zak_status, zak_due_date) VALUES (15, 10, '2024-01-25', '2024-01-25', '3', '2024-01-31');
INSERT INTO public.zakaz (zak_id, con_id, zak_date, zak_prep_date, zak_status, zak_due_date) VALUES (16, 5, '2024-01-24', '2024-01-24', '1', '2024-01-31');
INSERT INTO public.zakaz (zak_id, con_id, zak_date, zak_prep_date, zak_status, zak_due_date) VALUES (7, 7, '2021-01-06', '2022-08-07', '0', '2021-01-14');


--
-- TOC entry 4992 (class 0 OID 0)
-- Dependencies: 233
-- Name: cart_cart_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.cart_cart_id_seq', 34, true);


--
-- TOC entry 4993 (class 0 OID 0)
-- Dependencies: 216
-- Name: contract_con_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.contract_con_id_seq', 5, true);


--
-- TOC entry 4994 (class 0 OID 0)
-- Dependencies: 232
-- Name: customer_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.customer_id_seq', 5, true);


--
-- TOC entry 4995 (class 0 OID 0)
-- Dependencies: 218
-- Name: delivery_del_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.delivery_del_id_seq', 3, true);


--
-- TOC entry 4996 (class 0 OID 0)
-- Dependencies: 221
-- Name: employee_emp_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.employee_emp_id_seq', 1, false);


--
-- TOC entry 4997 (class 0 OID 0)
-- Dependencies: 223
-- Name: products_prd_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.products_prd_id_seq', 2, true);


--
-- TOC entry 4998 (class 0 OID 0)
-- Dependencies: 225
-- Name: warehouse_whs_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.warehouse_whs_id_seq', 1, false);


--
-- TOC entry 4999 (class 0 OID 0)
-- Dependencies: 227
-- Name: zakaz_zak_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.zakaz_zak_id_seq', 22, true);


--
-- TOC entry 4801 (class 2606 OID 24768)
-- Name: cart cart_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.cart
    ADD CONSTRAINT cart_pkey PRIMARY KEY (cart_id);


--
-- TOC entry 4789 (class 2606 OID 24740)
-- Name: customer customer_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.customer
    ADD CONSTRAINT customer_pkey PRIMARY KEY (cus_id);


--
-- TOC entry 4765 (class 2606 OID 24582)
-- Name: contract pk_contract; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.contract
    ADD CONSTRAINT pk_contract PRIMARY KEY (con_id);


--
-- TOC entry 4769 (class 2606 OID 24592)
-- Name: delivery pk_delivery; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.delivery
    ADD CONSTRAINT pk_delivery PRIMARY KEY (del_id);


--
-- TOC entry 4774 (class 2606 OID 24599)
-- Name: doing pk_doing; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.doing
    ADD CONSTRAINT pk_doing PRIMARY KEY (del_id, emp_id);


--
-- TOC entry 4777 (class 2606 OID 24609)
-- Name: employee pk_employee; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.employee
    ADD CONSTRAINT pk_employee PRIMARY KEY (emp_id);


--
-- TOC entry 4779 (class 2606 OID 24619)
-- Name: products pk_products; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.products
    ADD CONSTRAINT pk_products PRIMARY KEY (prd_id);


--
-- TOC entry 4791 (class 2606 OID 24649)
-- Name: wartoprod pk_quantity; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.wartoprod
    ADD CONSTRAINT pk_quantity PRIMARY KEY (whs_id, prd_id);


--
-- TOC entry 4798 (class 2606 OID 24657)
-- Name: prodtozak pk_quantity2; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.prodtozak
    ADD CONSTRAINT pk_quantity2 PRIMARY KEY (zak_id, prd_id);


--
-- TOC entry 4782 (class 2606 OID 24627)
-- Name: warehouse pk_warehouse; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.warehouse
    ADD CONSTRAINT pk_warehouse PRIMARY KEY (whs_id);


--
-- TOC entry 4786 (class 2606 OID 24635)
-- Name: zakaz pk_zakaz; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.zakaz
    ADD CONSTRAINT pk_zakaz PRIMARY KEY (zak_id);


--
-- TOC entry 4803 (class 2606 OID 24807)
-- Name: user_passwords user_passwords_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.user_passwords
    ADD CONSTRAINT user_passwords_pkey PRIMARY KEY (cus_id);


--
-- TOC entry 4784 (class 1259 OID 24637)
-- Name: Makes out by_FK; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX "Makes out by_FK" ON public.zakaz USING btree (con_id);


--
-- TOC entry 4762 (class 1259 OID 24583)
-- Name: contract_pk; Type: INDEX; Schema: public; Owner: postgres
--

CREATE UNIQUE INDEX contract_pk ON public.contract USING btree (con_id);


--
-- TOC entry 4766 (class 1259 OID 24593)
-- Name: delivery_pk; Type: INDEX; Schema: public; Owner: postgres
--

CREATE UNIQUE INDEX delivery_pk ON public.delivery USING btree (del_id);


--
-- TOC entry 4770 (class 1259 OID 24601)
-- Name: doing2_fk; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX doing2_fk ON public.doing USING btree (emp_id);


--
-- TOC entry 4771 (class 1259 OID 24602)
-- Name: doing_fk; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX doing_fk ON public.doing USING btree (del_id);


--
-- TOC entry 4772 (class 1259 OID 24600)
-- Name: doing_pk; Type: INDEX; Schema: public; Owner: postgres
--

CREATE UNIQUE INDEX doing_pk ON public.doing USING btree (del_id, emp_id);


--
-- TOC entry 4775 (class 1259 OID 24610)
-- Name: employee_pk; Type: INDEX; Schema: public; Owner: postgres
--

CREATE UNIQUE INDEX employee_pk ON public.employee USING btree (emp_id);


--
-- TOC entry 4763 (class 1259 OID 24584)
-- Name: etching_fk; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX etching_fk ON public.contract USING btree (emp_id);


--
-- TOC entry 4795 (class 1259 OID 24660)
-- Name: include2_fk; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX include2_fk ON public.prodtozak USING btree (zak_id);


--
-- TOC entry 4796 (class 1259 OID 24659)
-- Name: include_fk; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX include_fk ON public.prodtozak USING btree (prd_id);


--
-- TOC entry 4767 (class 1259 OID 24594)
-- Name: make out by2_FK; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX "make out by2_FK" ON public.delivery USING btree (zak_id);


--
-- TOC entry 4780 (class 1259 OID 24620)
-- Name: products_pk; Type: INDEX; Schema: public; Owner: postgres
--

CREATE UNIQUE INDEX products_pk ON public.products USING btree (prd_id);


--
-- TOC entry 4799 (class 1259 OID 24658)
-- Name: quantity2_pk; Type: INDEX; Schema: public; Owner: postgres
--

CREATE UNIQUE INDEX quantity2_pk ON public.prodtozak USING btree (zak_id, prd_id);


--
-- TOC entry 4792 (class 1259 OID 24650)
-- Name: quantity_pk; Type: INDEX; Schema: public; Owner: postgres
--

CREATE UNIQUE INDEX quantity_pk ON public.wartoprod USING btree (whs_id, prd_id);


--
-- TOC entry 4793 (class 1259 OID 24652)
-- Name: stored2_fk; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX stored2_fk ON public.wartoprod USING btree (whs_id);


--
-- TOC entry 4794 (class 1259 OID 24651)
-- Name: stored_fk; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX stored_fk ON public.wartoprod USING btree (prd_id);


--
-- TOC entry 4783 (class 1259 OID 24628)
-- Name: warehouse_pk; Type: INDEX; Schema: public; Owner: postgres
--

CREATE UNIQUE INDEX warehouse_pk ON public.warehouse USING btree (whs_id);


--
-- TOC entry 4787 (class 1259 OID 24636)
-- Name: zakaz_pk; Type: INDEX; Schema: public; Owner: postgres
--

CREATE UNIQUE INDEX zakaz_pk ON public.zakaz USING btree (zak_id);


--
-- TOC entry 4814 (class 2620 OID 24751)
-- Name: prodtozak update_delivery_cost_trigger; Type: TRIGGER; Schema: public; Owner: postgres
--

CREATE TRIGGER update_delivery_cost_trigger AFTER INSERT OR UPDATE ON public.prodtozak FOR EACH ROW EXECUTE FUNCTION public.update_delivery_cost();


--
-- TOC entry 4813 (class 2620 OID 24746)
-- Name: delivery update_zakaz_trigger; Type: TRIGGER; Schema: public; Owner: postgres
--

CREATE TRIGGER update_zakaz_trigger AFTER INSERT OR UPDATE ON public.delivery FOR EACH ROW EXECUTE FUNCTION public.update_zakaz_status();


--
-- TOC entry 4805 (class 2606 OID 24671)
-- Name: delivery FK_DELIVERY_MAKE OUT _ZAKAZ; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.delivery
    ADD CONSTRAINT "FK_DELIVERY_MAKE OUT _ZAKAZ" FOREIGN KEY (zak_id) REFERENCES public.zakaz(zak_id) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 4808 (class 2606 OID 24686)
-- Name: zakaz FK_ZAKAZ_MAKES OUT_CONTRACT; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.zakaz
    ADD CONSTRAINT "FK_ZAKAZ_MAKES OUT_CONTRACT" FOREIGN KEY (con_id) REFERENCES public.contract(con_id) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 4804 (class 2606 OID 24661)
-- Name: contract fk_contract_etching_employee; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.contract
    ADD CONSTRAINT fk_contract_etching_employee FOREIGN KEY (emp_id) REFERENCES public.employee(emp_id) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 4806 (class 2606 OID 24681)
-- Name: doing fk_doing_doing2_employee; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.doing
    ADD CONSTRAINT fk_doing_doing2_employee FOREIGN KEY (emp_id) REFERENCES public.employee(emp_id) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 4807 (class 2606 OID 24676)
-- Name: doing fk_doing_doing_delivery; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.doing
    ADD CONSTRAINT fk_doing_doing_delivery FOREIGN KEY (del_id) REFERENCES public.delivery(del_id) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 4811 (class 2606 OID 24711)
-- Name: prodtozak fk_quantity_include2_zakaz; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.prodtozak
    ADD CONSTRAINT fk_quantity_include2_zakaz FOREIGN KEY (zak_id) REFERENCES public.zakaz(zak_id) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 4812 (class 2606 OID 24706)
-- Name: prodtozak fk_quantity_include_products; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.prodtozak
    ADD CONSTRAINT fk_quantity_include_products FOREIGN KEY (prd_id) REFERENCES public.products(prd_id) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 4809 (class 2606 OID 24701)
-- Name: wartoprod fk_quantity_stored2_warehous; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.wartoprod
    ADD CONSTRAINT fk_quantity_stored2_warehous FOREIGN KEY (whs_id) REFERENCES public.warehouse(whs_id) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 4810 (class 2606 OID 24696)
-- Name: wartoprod fk_quantity_stored_products; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.wartoprod
    ADD CONSTRAINT fk_quantity_stored_products FOREIGN KEY (prd_id) REFERENCES public.products(prd_id) ON UPDATE RESTRICT ON DELETE RESTRICT;


-- Completed on 2024-06-10 03:02:41

--
-- PostgreSQL database dump complete
--

