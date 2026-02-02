--
-- PostgreSQL database dump
--

-- Dumped from database version 17.5
-- Dumped by pg_dump version 17.5

-- Started on 2026-02-02 20:02:16

SET statement_timeout = 0;
SET lock_timeout = 0;
SET idle_in_transaction_session_timeout = 0;
SET transaction_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SELECT pg_catalog.set_config('search_path', '', false);
SET check_function_bodies = false;
SET xmloption = content;
SET client_min_messages = warning;
SET row_security = off;

--
-- TOC entry 4 (class 2615 OID 2200)
-- Name: public; Type: SCHEMA; Schema: -; Owner: pg_database_owner
--

CREATE SCHEMA public;


ALTER SCHEMA public OWNER TO pg_database_owner;

--
-- TOC entry 5087 (class 0 OID 0)
-- Dependencies: 4
-- Name: SCHEMA public; Type: COMMENT; Schema: -; Owner: pg_database_owner
--

COMMENT ON SCHEMA public IS 'standard public schema';


--
-- TOC entry 889 (class 1247 OID 45538)
-- Name: tb_tipo_banco; Type: TYPE; Schema: public; Owner: postgres
--

CREATE TYPE public.tb_tipo_banco AS ENUM (
    'postgres',
    'mysql',
    'oracle',
    'sqlserver',
    'odbc'
);


ALTER TYPE public.tb_tipo_banco OWNER TO postgres;

SET default_tablespace = '';

SET default_table_access_method = heap;

--
-- TOC entry 224 (class 1259 OID 45520)
-- Name: connections; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.connections (
    id integer NOT NULL,
    name character varying NOT NULL,
    driver character varying,
    host character varying,
    port character varying,
    database character varying,
    username character varying,
    password_enc character varying,
    extras character varying,
    json_config text
);


ALTER TABLE public.connections OWNER TO postgres;

--
-- TOC entry 223 (class 1259 OID 45519)
-- Name: connections_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.connections_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.connections_id_seq OWNER TO postgres;

--
-- TOC entry 5088 (class 0 OID 0)
-- Dependencies: 223
-- Name: connections_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.connections_id_seq OWNED BY public.connections.id;


--
-- TOC entry 245 (class 1259 OID 45677)
-- Name: logs_sistema; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.logs_sistema (
    id integer NOT NULL,
    nivel character varying(20) DEFAULT 'info'::character varying NOT NULL,
    categoria character varying(50),
    mensagem text,
    contexto jsonb,
    usuario_id integer,
    ip character varying(45),
    created_at timestamp without time zone DEFAULT CURRENT_TIMESTAMP
);


ALTER TABLE public.logs_sistema OWNER TO postgres;

--
-- TOC entry 244 (class 1259 OID 45676)
-- Name: logs_sistema_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.logs_sistema_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.logs_sistema_id_seq OWNER TO postgres;

--
-- TOC entry 5089 (class 0 OID 0)
-- Dependencies: 244
-- Name: logs_sistema_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.logs_sistema_id_seq OWNED BY public.logs_sistema.id;


--
-- TOC entry 226 (class 1259 OID 45529)
-- Name: schedules; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.schedules (
    id integer NOT NULL,
    name character varying,
    rotina character varying,
    bloco character varying,
    sql text,
    connection_json text,
    cron character varying,
    interval_seconds integer
);


ALTER TABLE public.schedules OWNER TO postgres;

--
-- TOC entry 225 (class 1259 OID 45528)
-- Name: schedules_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.schedules_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.schedules_id_seq OWNER TO postgres;

--
-- TOC entry 5090 (class 0 OID 0)
-- Dependencies: 225
-- Name: schedules_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.schedules_id_seq OWNED BY public.schedules.id;


--
-- TOC entry 218 (class 1259 OID 45485)
-- Name: tb_arquivos; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.tb_arquivos (
    id integer NOT NULL,
    nome text NOT NULL,
    tamanho bigint,
    dados bytea,
    criado_em timestamp with time zone DEFAULT now(),
    rotina text,
    bloco text
);


ALTER TABLE public.tb_arquivos OWNER TO postgres;

--
-- TOC entry 217 (class 1259 OID 45484)
-- Name: tb_arquivos_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.tb_arquivos_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.tb_arquivos_id_seq OWNER TO postgres;

--
-- TOC entry 5091 (class 0 OID 0)
-- Dependencies: 217
-- Name: tb_arquivos_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.tb_arquivos_id_seq OWNED BY public.tb_arquivos.id;


--
-- TOC entry 220 (class 1259 OID 45495)
-- Name: tb_auditoria_rotina; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.tb_auditoria_rotina (
    id integer NOT NULL,
    rotina text,
    bloco text,
    inicio timestamp with time zone,
    fim timestamp with time zone,
    status text,
    resultado text,
    id_arquivo integer
);


ALTER TABLE public.tb_auditoria_rotina OWNER TO postgres;

--
-- TOC entry 219 (class 1259 OID 45494)
-- Name: tb_auditoria_rotina_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.tb_auditoria_rotina_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.tb_auditoria_rotina_id_seq OWNER TO postgres;

--
-- TOC entry 5092 (class 0 OID 0)
-- Dependencies: 219
-- Name: tb_auditoria_rotina_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.tb_auditoria_rotina_id_seq OWNED BY public.tb_auditoria_rotina.id;


--
-- TOC entry 234 (class 1259 OID 45592)
-- Name: tb_blocos_rotina; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.tb_blocos_rotina (
    id bigint NOT NULL,
    id_rotina bigint NOT NULL,
    codigo_bloco text NOT NULL,
    ordem integer DEFAULT 1 NOT NULL,
    script_sql text NOT NULL,
    tipo_bloco text NOT NULL,
    data_criacao timestamp with time zone DEFAULT now() NOT NULL
);


ALTER TABLE public.tb_blocos_rotina OWNER TO postgres;

--
-- TOC entry 233 (class 1259 OID 45591)
-- Name: tb_blocos_rotina_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

ALTER TABLE public.tb_blocos_rotina ALTER COLUMN id ADD GENERATED ALWAYS AS IDENTITY (
    SEQUENCE NAME public.tb_blocos_rotina_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1
);


--
-- TOC entry 236 (class 1259 OID 45607)
-- Name: tb_logs_execucao; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.tb_logs_execucao (
    id bigint NOT NULL,
    id_rotina bigint,
    data_inicio timestamp with time zone DEFAULT now() NOT NULL,
    data_fim timestamp with time zone,
    status text,
    mensagem_erro text,
    meta jsonb,
    id_usuario integer,
    duracao_ms integer,
    blocos_executados integer DEFAULT 0,
    blocos_sucesso integer DEFAULT 0,
    blocos_falha integer DEFAULT 0,
    caminho_csv text,
    detalhes_json jsonb,
    registros_processados integer DEFAULT 0
);


ALTER TABLE public.tb_logs_execucao OWNER TO postgres;

--
-- TOC entry 235 (class 1259 OID 45606)
-- Name: tb_logs_execucao_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

ALTER TABLE public.tb_logs_execucao ALTER COLUMN id ADD GENERATED ALWAYS AS IDENTITY (
    SEQUENCE NAME public.tb_logs_execucao_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1
);


--
-- TOC entry 238 (class 1259 OID 45632)
-- Name: tb_logs_sistema; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.tb_logs_sistema (
    id integer NOT NULL,
    nivel character varying(20) NOT NULL,
    mensagem text NOT NULL,
    contexto jsonb DEFAULT '{}'::jsonb,
    canal character varying(100) DEFAULT 'app'::character varying,
    id_usuario integer,
    ip_address character varying(45),
    user_agent text,
    request_uri text,
    request_method character varying(10),
    trace_id character varying(50),
    criado_em timestamp with time zone DEFAULT CURRENT_TIMESTAMP
);


ALTER TABLE public.tb_logs_sistema OWNER TO postgres;

--
-- TOC entry 237 (class 1259 OID 45631)
-- Name: tb_logs_sistema_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.tb_logs_sistema_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.tb_logs_sistema_id_seq OWNER TO postgres;

--
-- TOC entry 5093 (class 0 OID 0)
-- Dependencies: 237
-- Name: tb_logs_sistema_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.tb_logs_sistema_id_seq OWNED BY public.tb_logs_sistema.id;


--
-- TOC entry 240 (class 1259 OID 45647)
-- Name: tb_metricas_sistema; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.tb_metricas_sistema (
    id integer NOT NULL,
    nome character varying(100) NOT NULL,
    valor numeric(20,4) NOT NULL,
    tipo character varying(20) NOT NULL,
    labels jsonb DEFAULT '{}'::jsonb,
    criado_em timestamp with time zone DEFAULT CURRENT_TIMESTAMP
);


ALTER TABLE public.tb_metricas_sistema OWNER TO postgres;

--
-- TOC entry 239 (class 1259 OID 45646)
-- Name: tb_metricas_sistema_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.tb_metricas_sistema_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.tb_metricas_sistema_id_seq OWNER TO postgres;

--
-- TOC entry 5094 (class 0 OID 0)
-- Dependencies: 239
-- Name: tb_metricas_sistema_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.tb_metricas_sistema_id_seq OWNED BY public.tb_metricas_sistema.id;


--
-- TOC entry 230 (class 1259 OID 45561)
-- Name: tb_perfis_conexao; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.tb_perfis_conexao (
    id bigint NOT NULL,
    nome_conexao text NOT NULL,
    tipo_banco public.tb_tipo_banco NOT NULL,
    host text NOT NULL,
    porta integer,
    nome_banco text,
    usuario text,
    senha_encriptada text,
    parametros_extras jsonb,
    data_criacao timestamp with time zone DEFAULT now() NOT NULL
);


ALTER TABLE public.tb_perfis_conexao OWNER TO postgres;

--
-- TOC entry 229 (class 1259 OID 45560)
-- Name: tb_perfis_conexao_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

ALTER TABLE public.tb_perfis_conexao ALTER COLUMN id ADD GENERATED ALWAYS AS IDENTITY (
    SEQUENCE NAME public.tb_perfis_conexao_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1
);


--
-- TOC entry 232 (class 1259 OID 45572)
-- Name: tb_rotinas; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.tb_rotinas (
    id bigint NOT NULL,
    nome text NOT NULL,
    descricao text,
    id_conexao bigint NOT NULL,
    id_usuario_criador bigint,
    esta_executando boolean DEFAULT false NOT NULL,
    ultima_verificacao timestamp with time zone,
    webhook_sucesso text,
    webhook_falha text,
    data_criacao timestamp with time zone DEFAULT now() NOT NULL,
    agendamento_cron text,
    ativa boolean DEFAULT false,
    proxima_execucao timestamp with time zone,
    tentativas_falha integer DEFAULT 0,
    max_tentativas integer DEFAULT 3,
    ultima_execucao timestamp with time zone,
    data_inicio timestamp without time zone,
    data_fim timestamp without time zone,
    datas_ignorar_json jsonb,
    ignorar_feriados boolean DEFAULT false,
    timeout integer DEFAULT 300,
    notificar_falha boolean DEFAULT true
);


ALTER TABLE public.tb_rotinas OWNER TO postgres;

--
-- TOC entry 5095 (class 0 OID 0)
-- Dependencies: 232
-- Name: COLUMN tb_rotinas.data_inicio; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.tb_rotinas.data_inicio IS 'Data e hora de início do agendamento (quando começar a executar)';


--
-- TOC entry 5096 (class 0 OID 0)
-- Dependencies: 232
-- Name: COLUMN tb_rotinas.data_fim; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.tb_rotinas.data_fim IS 'Data e hora de término do agendamento (quando parar de executar)';


--
-- TOC entry 5097 (class 0 OID 0)
-- Dependencies: 232
-- Name: COLUMN tb_rotinas.datas_ignorar_json; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.tb_rotinas.datas_ignorar_json IS 'Array JSON com datas específicas para não executar';


--
-- TOC entry 5098 (class 0 OID 0)
-- Dependencies: 232
-- Name: COLUMN tb_rotinas.ignorar_feriados; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.tb_rotinas.ignorar_feriados IS 'Se deve ignorar feriados nacionais brasileiros';


--
-- TOC entry 5099 (class 0 OID 0)
-- Dependencies: 232
-- Name: COLUMN tb_rotinas.timeout; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.tb_rotinas.timeout IS 'Timeout máximo de execução em segundos (padrão: 300s = 5min)';


--
-- TOC entry 5100 (class 0 OID 0)
-- Dependencies: 232
-- Name: COLUMN tb_rotinas.notificar_falha; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.tb_rotinas.notificar_falha IS 'Se deve notificar quando houver falha na execução';


--
-- TOC entry 231 (class 1259 OID 45571)
-- Name: tb_rotinas_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

ALTER TABLE public.tb_rotinas ALTER COLUMN id ADD GENERATED ALWAYS AS IDENTITY (
    SEQUENCE NAME public.tb_rotinas_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1
);


--
-- TOC entry 228 (class 1259 OID 45550)
-- Name: tb_usuarios; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.tb_usuarios (
    id bigint NOT NULL,
    nome_usuario text NOT NULL,
    senha_hash text,
    eh_ldap boolean DEFAULT false NOT NULL,
    nivel_acesso text DEFAULT 'user'::text NOT NULL,
    data_criacao timestamp with time zone DEFAULT now() NOT NULL,
    ldap_auth boolean DEFAULT false
);


ALTER TABLE public.tb_usuarios OWNER TO postgres;

--
-- TOC entry 227 (class 1259 OID 45549)
-- Name: tb_usuarios_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

ALTER TABLE public.tb_usuarios ALTER COLUMN id ADD GENERATED ALWAYS AS IDENTITY (
    SEQUENCE NAME public.tb_usuarios_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1
);


--
-- TOC entry 242 (class 1259 OID 45658)
-- Name: tb_worker_heartbeat; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.tb_worker_heartbeat (
    id integer NOT NULL,
    worker_id character varying(100) NOT NULL,
    hostname character varying(255),
    pid integer,
    status character varying(20) DEFAULT 'ativo'::character varying,
    ultimo_heartbeat timestamp with time zone DEFAULT CURRENT_TIMESTAMP,
    rotinas_processadas integer DEFAULT 0,
    criado_em timestamp with time zone DEFAULT CURRENT_TIMESTAMP
);


ALTER TABLE public.tb_worker_heartbeat OWNER TO postgres;

--
-- TOC entry 241 (class 1259 OID 45657)
-- Name: tb_worker_heartbeat_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.tb_worker_heartbeat_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.tb_worker_heartbeat_id_seq OWNER TO postgres;

--
-- TOC entry 5101 (class 0 OID 0)
-- Dependencies: 241
-- Name: tb_worker_heartbeat_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.tb_worker_heartbeat_id_seq OWNED BY public.tb_worker_heartbeat.id;


--
-- TOC entry 222 (class 1259 OID 45509)
-- Name: users; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.users (
    id integer NOT NULL,
    username character varying NOT NULL,
    password_hash character varying NOT NULL
);


ALTER TABLE public.users OWNER TO postgres;

--
-- TOC entry 221 (class 1259 OID 45508)
-- Name: users_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.users_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.users_id_seq OWNER TO postgres;

--
-- TOC entry 5102 (class 0 OID 0)
-- Dependencies: 221
-- Name: users_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.users_id_seq OWNED BY public.users.id;


--
-- TOC entry 243 (class 1259 OID 45672)
-- Name: vw_status_workers; Type: VIEW; Schema: public; Owner: postgres
--

CREATE VIEW public.vw_status_workers AS
 SELECT worker_id,
    hostname,
    pid,
    status,
    ultimo_heartbeat,
    rotinas_processadas,
    EXTRACT(epoch FROM (CURRENT_TIMESTAMP - ultimo_heartbeat)) AS segundos_desde_heartbeat,
        CASE
            WHEN (ultimo_heartbeat > (CURRENT_TIMESTAMP - '00:01:00'::interval)) THEN 'saudavel'::text
            WHEN (ultimo_heartbeat > (CURRENT_TIMESTAMP - '00:05:00'::interval)) THEN 'atencao'::text
            ELSE 'critico'::text
        END AS saude
   FROM public.tb_worker_heartbeat
  ORDER BY ultimo_heartbeat DESC;


ALTER VIEW public.vw_status_workers OWNER TO postgres;

--
-- TOC entry 4818 (class 2604 OID 45523)
-- Name: connections id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.connections ALTER COLUMN id SET DEFAULT nextval('public.connections_id_seq'::regclass);


--
-- TOC entry 4852 (class 2604 OID 45680)
-- Name: logs_sistema id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.logs_sistema ALTER COLUMN id SET DEFAULT nextval('public.logs_sistema_id_seq'::regclass);


--
-- TOC entry 4819 (class 2604 OID 45532)
-- Name: schedules id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.schedules ALTER COLUMN id SET DEFAULT nextval('public.schedules_id_seq'::regclass);


--
-- TOC entry 4814 (class 2604 OID 45488)
-- Name: tb_arquivos id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tb_arquivos ALTER COLUMN id SET DEFAULT nextval('public.tb_arquivos_id_seq'::regclass);


--
-- TOC entry 4816 (class 2604 OID 45498)
-- Name: tb_auditoria_rotina id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tb_auditoria_rotina ALTER COLUMN id SET DEFAULT nextval('public.tb_auditoria_rotina_id_seq'::regclass);


--
-- TOC entry 4840 (class 2604 OID 45635)
-- Name: tb_logs_sistema id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tb_logs_sistema ALTER COLUMN id SET DEFAULT nextval('public.tb_logs_sistema_id_seq'::regclass);


--
-- TOC entry 4844 (class 2604 OID 45650)
-- Name: tb_metricas_sistema id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tb_metricas_sistema ALTER COLUMN id SET DEFAULT nextval('public.tb_metricas_sistema_id_seq'::regclass);


--
-- TOC entry 4847 (class 2604 OID 45661)
-- Name: tb_worker_heartbeat id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tb_worker_heartbeat ALTER COLUMN id SET DEFAULT nextval('public.tb_worker_heartbeat_id_seq'::regclass);


--
-- TOC entry 4817 (class 2604 OID 45512)
-- Name: users id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.users ALTER COLUMN id SET DEFAULT nextval('public.users_id_seq'::regclass);


--
-- TOC entry 5061 (class 0 OID 45520)
-- Dependencies: 224
-- Data for Name: connections; Type: TABLE DATA; Schema: public; Owner: postgres
--

INSERT INTO public.connections VALUES (1, 'local_sqlite', 'sqlite', NULL, '', 'C:\Users\caio.barros\OneDrive\Cloud\PESSOAL\CAIO\NOTEBOOK\PROJETOS\DMC-DATALOAD\backend\test_target.db', NULL, NULL, '', '{"driver": "sqlite", "database": "C:\\Users\\caio.barros\\OneDrive\\Cloud\\PESSOAL\\CAIO\\NOTEBOOK\\PROJETOS\\DMC-DATALOAD\\backend\\test_target.db"}');


--
-- TOC entry 5081 (class 0 OID 45677)
-- Dependencies: 245
-- Data for Name: logs_sistema; Type: TABLE DATA; Schema: public; Owner: postgres
--

INSERT INTO public.logs_sistema VALUES (1, 'info', 'scheduler', 'Scheduler Worker iniciado (PID: 2260)', NULL, NULL, NULL, '2026-02-02 17:15:56.320132');
INSERT INTO public.logs_sistema VALUES (2, 'debug', 'scheduler', 'Verificando rotinas agendadas...', NULL, NULL, NULL, '2026-02-02 17:15:56.324557');
INSERT INTO public.logs_sistema VALUES (3, 'error', 'scheduler', 'Erro na verificação: SQLSTATE[42P01]: Undefined table: 7 ERRO:  relação "rotinas" não existe
LINE 8:                     FROM rotinas r
                                 ^', NULL, NULL, NULL, '2026-02-02 17:15:56.32839');
INSERT INTO public.logs_sistema VALUES (4, 'info', 'scheduler', 'Scheduler iniciado', NULL, 1, NULL, '2026-02-02 17:15:56.673136');
INSERT INTO public.logs_sistema VALUES (5, 'warning', 'scheduler', 'Scheduler parado', NULL, 1, NULL, '2026-02-02 17:16:03.033107');
INSERT INTO public.logs_sistema VALUES (6, 'info', 'scheduler', 'Rotina 21 desativada', NULL, 1, NULL, '2026-02-02 17:19:21.44865');
INSERT INTO public.logs_sistema VALUES (7, 'info', 'scheduler', 'Agendamento configurado para rotina ID 21: 00 08 */3 * *', NULL, 1, NULL, '2026-02-02 17:23:14.372103');
INSERT INTO public.logs_sistema VALUES (8, 'info', 'scheduler', 'Agendamento configurado para rotina ID 21: 00 08 */2 * *', NULL, 1, NULL, '2026-02-02 17:33:36.588118');


--
-- TOC entry 5063 (class 0 OID 45529)
-- Dependencies: 226
-- Data for Name: schedules; Type: TABLE DATA; Schema: public; Owner: postgres
--



--
-- TOC entry 5055 (class 0 OID 45485)
-- Dependencies: 218
-- Data for Name: tb_arquivos; Type: TABLE DATA; Schema: public; Owner: postgres
--

INSERT INTO public.tb_arquivos VALUES (1, 'dt_b1_20260130T120315_04b20c4be64b40f19e6ef66216200c1b.csv', 66, '\x69643b6e616d650d0a313b610d0a323b620d0a333b630d0a343b630d0a353b630d0a363b630d0a373b630d0a383b630d0a393b630d0a31303b630d0a31313b630d0a', '2026-01-30 09:03:15.786811-03', 'dt', 'b1');
INSERT INTO public.tb_arquivos VALUES (2, 't1_b1_20260130T120322_c378c6092e074789a55086478283a334.csv', 66, '\x69643b6e616d650d0a313b610d0a323b620d0a333b630d0a343b630d0a353b630d0a363b630d0a373b630d0a383b630d0a393b630d0a31303b630d0a31313b630d0a', '2026-01-30 09:03:22.509801-03', 't1', 'b1');
INSERT INTO public.tb_arquivos VALUES (3, 'sch_b_20260130T120327_fe62995b8c6e4aa58a877b7dc5f4b85c.csv', 72, '\x69643b6e616d650d0a313b610d0a323b620d0a333b630d0a343b630d0a353b630d0a363b630d0a373b630d0a383b630d0a393b630d0a31303b630d0a31313b630d0a31323b630d0a', '2026-01-30 09:03:27.586165-03', 'sch', 'b');


--
-- TOC entry 5057 (class 0 OID 45495)
-- Dependencies: 220
-- Data for Name: tb_auditoria_rotina; Type: TABLE DATA; Schema: public; Owner: postgres
--

INSERT INTO public.tb_auditoria_rotina VALUES (1, 'dt', 'b1', '2026-01-30 12:03:15.698905-03', '2026-01-30 12:03:15.791777-03', 'SUCCESS', 'id;name
1;a
2;b
3;c
4;c
5;c
6;c
7;c
8;c
9;c
10;c
', 1);
INSERT INTO public.tb_auditoria_rotina VALUES (2, 't1', 'b1', '2026-01-30 12:03:22.5071-03', '2026-01-30 12:03:22.511569-03', 'SUCCESS', 'id;name
1;a
2;b
3;c
4;c
5;c
6;c
7;c
8;c
9;c
10;c
', 2);
INSERT INTO public.tb_auditoria_rotina VALUES (3, 't1', 'b2', '2026-01-30 12:03:22.529659-03', '2026-01-30 12:03:22.542487-03', 'SUCCESS', 'Linhas afetadas: 1', NULL);
INSERT INTO public.tb_auditoria_rotina VALUES (4, 'sch', 'b', '2026-01-30 12:03:27.582912-03', '2026-01-30 12:03:27.638239-03', 'SUCCESS', 'id;name
1;a
2;b
3;c
4;c
5;c
6;c
7;c
8;c
9;c
10;c
', 3);
INSERT INTO public.tb_auditoria_rotina VALUES (5, '6', 'B_RUN', '2026-01-30 11:29:14.034233-03', NULL, 'sucesso', 'Arquivo gerado: C:\xampp\htdocs\DMC-DATALOAD\app\Servicos/../../storage/logs\execucao_6_B_RUN_1769783354.csv (Linhas: 1)', NULL);


--
-- TOC entry 5071 (class 0 OID 45592)
-- Dependencies: 234
-- Data for Name: tb_blocos_rotina; Type: TABLE DATA; Schema: public; Owner: postgres
--



--
-- TOC entry 5073 (class 0 OID 45607)
-- Dependencies: 236
-- Data for Name: tb_logs_execucao; Type: TABLE DATA; Schema: public; Owner: postgres
--

INSERT INTO public.tb_logs_execucao OVERRIDING SYSTEM VALUE VALUES (6, NULL, '2026-01-30 11:29:14.035492-03', '2026-01-30 11:29:14.035492-03', 'sucesso', NULL, '[{"res": {"linhas": 1, "arquivo": "C:\\xampp\\htdocs\\DMC-DATALOAD\\app\\Servicos/../../storage/logs\\execucao_6_B_RUN_1769783354.csv", "sucesso": true, "resultado": "Arquivo gerado: C:\\xampp\\htdocs\\DMC-DATALOAD\\app\\Servicos/../../storage/logs\\execucao_6_B_RUN_1769783354.csv (Linhas: 1)"}, "bloco": "B_RUN"}]', NULL, NULL, 0, 0, 0, NULL, NULL, 0);
INSERT INTO public.tb_logs_execucao OVERRIDING SYSTEM VALUE VALUES (7, NULL, '2026-01-30 11:46:40.730096-03', '2026-01-30 11:46:40.730096-03', 'erro', 'Não foi possível conectar ao banco alvo', NULL, NULL, NULL, 0, 0, 0, NULL, NULL, 0);
INSERT INTO public.tb_logs_execucao OVERRIDING SYSTEM VALUE VALUES (9, NULL, '2026-02-02 11:11:46.256731-03', '2026-02-02 11:11:46.256731-03', 'erro', 'Não foi possível conectar ao banco alvo', NULL, NULL, NULL, 0, 0, 0, NULL, NULL, 0);
INSERT INTO public.tb_logs_execucao OVERRIDING SYSTEM VALUE VALUES (10, NULL, '2026-02-02 13:18:29.371874-03', '2026-02-02 13:18:29.371874-03', 'erro', 'Não foi possível conectar ao banco alvo', NULL, NULL, NULL, 0, 0, 0, NULL, NULL, 0);
INSERT INTO public.tb_logs_execucao OVERRIDING SYSTEM VALUE VALUES (8, NULL, '2026-01-30 13:55:40.959232-03', '2026-01-30 13:55:40.959232-03', 'erro', 'Não foi possível conectar ao banco alvo', NULL, NULL, NULL, 0, 0, 0, NULL, NULL, 0);
INSERT INTO public.tb_logs_execucao OVERRIDING SYSTEM VALUE VALUES (1, NULL, '2026-01-30 11:11:46.459892-03', '2026-01-30 11:11:46.459892-03', 'erro', 'Não foi possível conectar ao banco alvo', NULL, NULL, NULL, 0, 0, 0, NULL, NULL, 0);
INSERT INTO public.tb_logs_execucao OVERRIDING SYSTEM VALUE VALUES (2, NULL, '2026-01-30 11:12:12.913645-03', '2026-01-30 11:12:12.913645-03', 'erro', 'Não foi possível conectar ao banco alvo', NULL, NULL, NULL, 0, 0, 0, NULL, NULL, 0);
INSERT INTO public.tb_logs_execucao OVERRIDING SYSTEM VALUE VALUES (3, NULL, '2026-01-30 11:14:20.837348-03', '2026-01-30 11:14:20.837348-03', 'erro', 'Não foi possível conectar ao banco alvo', NULL, NULL, NULL, 0, 0, 0, NULL, NULL, 0);
INSERT INTO public.tb_logs_execucao OVERRIDING SYSTEM VALUE VALUES (4, NULL, '2026-01-30 11:16:43.812746-03', '2026-01-30 11:16:43.812746-03', 'erro', 'Não foi possível conectar ao banco alvo', NULL, NULL, NULL, 0, 0, 0, NULL, NULL, 0);
INSERT INTO public.tb_logs_execucao OVERRIDING SYSTEM VALUE VALUES (5, NULL, '2026-01-30 11:28:28.266212-03', '2026-01-30 11:28:28.266212-03', 'sucesso', NULL, '[{"res": {"erro": "SQLSTATE[42703]: Undefined column: 7 ERRO:  coluna \"id_rotina\" da relação \"tb_auditoria_rotina\" não existe\nLINE 1: INSERT INTO tb_auditoria_rotina (id_rotina, bloco_codigo, da...\n                                         ^", "sucesso": false}, "bloco": "B_RUN"}]', NULL, NULL, 0, 0, 0, NULL, NULL, 0);
INSERT INTO public.tb_logs_execucao OVERRIDING SYSTEM VALUE VALUES (15, NULL, '2026-02-02 16:02:02.787395-03', '2026-02-02 16:02:02.787395-03', 'falha', 'Não foi possível conectar ao banco alvo', '[]', NULL, 82, 0, 0, 0, NULL, NULL, 0);
INSERT INTO public.tb_logs_execucao OVERRIDING SYSTEM VALUE VALUES (11, NULL, '2026-02-02 15:19:52.732188-03', '2026-02-02 15:19:52.732188-03', 'sucesso', NULL, '[{"res": {"linhas": 3, "arquivo": "C:\\xampp\\htdocs\\DMC-DATALOAD\\app\\Servicos/../../storage/logs\\execucao_21_set1_1770056392.csv", "sucesso": true, "resultado": "Arquivo gerado: C:\\xampp\\htdocs\\DMC-DATALOAD\\app\\Servicos/../../storage/logs\\execucao_21_set1_1770056392.csv (Linhas: 3)"}, "bloco": "set1"}, {"res": {"linhas": 1, "sucesso": true, "resultado": "Linhas afetadas: 1"}, "bloco": ""}]', NULL, NULL, 0, 0, 0, NULL, NULL, 0);
INSERT INTO public.tb_logs_execucao OVERRIDING SYSTEM VALUE VALUES (12, NULL, '2026-02-02 15:37:37.418165-03', '2026-02-02 15:37:37.418165-03', 'sucesso', NULL, '[{"res": {"linhas": 3, "arquivo": "C:\\xampp\\htdocs\\DMC-DATALOAD\\app\\Servicos/../../storage/logs\\execucao_21_set1_1770057457.csv", "sucesso": true, "resultado": "Arquivo gerado: C:\\xampp\\htdocs\\DMC-DATALOAD\\app\\Servicos/../../storage/logs\\execucao_21_set1_1770057457.csv (Linhas: 3)"}, "bloco": "set1"}, {"res": {"linhas": 1, "sucesso": true, "resultado": "Linhas afetadas: 1"}, "bloco": ""}]', NULL, NULL, 0, 0, 0, NULL, NULL, 0);
INSERT INTO public.tb_logs_execucao OVERRIDING SYSTEM VALUE VALUES (13, NULL, '2026-02-02 15:53:56.603468-03', '2026-02-02 15:53:56.603468-03', 'sucesso', NULL, '[{"erro": null, "tipo": "SELECT", "bloco": "set1", "registros": 3, "resultado": "Arquivo gerado: C:\\xampp\\htdocs\\DMC-DATALOAD\\app\\Servicos/../../storage/logs\\execucao_21_set1_1770058436.csv (Linhas: 3)", "duracao_ms": 18}, {"erro": null, "tipo": "UPDATE", "bloco": "", "registros": 1, "resultado": "Linhas afetadas: 1", "duracao_ms": 8}]', NULL, 135, 2, 2, 0, NULL, NULL, 4);
INSERT INTO public.tb_logs_execucao OVERRIDING SYSTEM VALUE VALUES (14, NULL, '2026-02-02 16:01:01.994437-03', '2026-02-02 16:01:01.994437-03', 'sucesso', NULL, '[{"sql": "select * from public.content;", "erro": null, "tipo": "SELECT", "bloco": "set1", "ordem": 1, "status": "sucesso", "id_bloco": 35, "registros": 3, "resultado": "Arquivo gerado: C:\\xampp\\htdocs\\DMC-DATALOAD\\app\\Servicos/../../storage/logs\\execucao_21_set1_1770058861.csv (Linhas: 3)", "duracao_ms": 16, "arquivo_csv": "C:\\xampp\\htdocs\\DMC-DATALOAD\\app\\Servicos/../../storage/logs\\execucao_21_set1_1770058861.csv"}, {"sql": "update public.content set title = ''Solo Leveling'' where id = 15; ", "erro": null, "tipo": "UPDATE", "bloco": "", "ordem": 2, "status": "sucesso", "id_bloco": 36, "registros": 1, "resultado": "Linhas afetadas: 1", "duracao_ms": 7, "arquivo_csv": null}]', NULL, 123, 2, 2, 0, NULL, NULL, 4);
INSERT INTO public.tb_logs_execucao OVERRIDING SYSTEM VALUE VALUES (16, NULL, '2026-02-02 16:03:37.320246-03', '2026-02-02 16:03:37.320246-03', 'sucesso', NULL, '[{"sql": "select * from public.content limit 5;", "erro": null, "tipo": "SELECT", "bloco": "bloco1", "ordem": 1, "status": "sucesso", "id_bloco": 37, "registros": 3, "resultado": "Arquivo gerado: C:\\xampp\\htdocs\\DMC-DATALOAD\\app\\Servicos/../../storage/logs\\execucao_24_bloco1_1770059017.csv (Linhas: 3)", "duracao_ms": 21, "arquivo_csv": "C:\\xampp\\htdocs\\DMC-DATALOAD\\app\\Servicos/../../storage/logs\\execucao_24_bloco1_1770059017.csv"}, {"sql": "select * from tabela_que_nao_existe;", "erro": "SQLSTATE[42P01]: Undefined table: 7 ERRO:  relação \"tabela_que_nao_existe\" não existe\nLINE 1: select * from tabela_que_nao_existe;\n                      ^", "tipo": "SELECT", "bloco": "bloco2_erro", "ordem": 2, "status": "falha", "id_bloco": 38, "registros": 0, "resultado": "", "duracao_ms": 0, "arquivo_csv": null}, {"sql": "select * from public.content where id > 10;", "erro": null, "tipo": "SELECT", "bloco": "bloco3", "ordem": 3, "status": "sucesso", "id_bloco": 39, "registros": 3, "resultado": "Arquivo gerado: C:\\xampp\\htdocs\\DMC-DATALOAD\\app\\Servicos/../../storage/logs\\execucao_24_bloco3_1770059017.csv (Linhas: 3)", "duracao_ms": 19, "arquivo_csv": "C:\\xampp\\htdocs\\DMC-DATALOAD\\app\\Servicos/../../storage/logs\\execucao_24_bloco3_1770059017.csv"}]', NULL, 159, 3, 2, 1, NULL, NULL, 6);
INSERT INTO public.tb_logs_execucao OVERRIDING SYSTEM VALUE VALUES (17, NULL, '2026-02-02 16:46:05.623935-03', '2026-02-02 16:46:05.623935-03', 'sucesso', NULL, '[{"sql": "select * from public.content;", "erro": null, "tipo": "SELECT", "bloco": "set1", "ordem": 1, "status": "sucesso", "id_bloco": 35, "registros": 3, "resultado": "Arquivo gerado: C:\\xampp\\htdocs\\DMC-DATALOAD\\app\\Servicos/../../storage/logs\\execucao_21_set1_1770061565.csv (Linhas: 3)", "duracao_ms": 13, "arquivo_csv": "C:\\xampp\\htdocs\\DMC-DATALOAD\\app\\Servicos/../../storage/logs\\execucao_21_set1_1770061565.csv"}, {"sql": "update public.content set title = ''Solo Leveling'' where id = 15;", "erro": null, "tipo": "UPDATE", "bloco": "", "ordem": 2, "status": "sucesso", "id_bloco": 36, "registros": 1, "resultado": "Linhas afetadas: 1", "duracao_ms": 6, "arquivo_csv": null}]', NULL, 82, 2, 2, 0, NULL, NULL, 4);


--
-- TOC entry 5075 (class 0 OID 45632)
-- Dependencies: 238
-- Data for Name: tb_logs_sistema; Type: TABLE DATA; Schema: public; Owner: postgres
--

INSERT INTO public.tb_logs_sistema VALUES (1, 'INFO', 'Sistema iniciado com sucesso', '{"modulo": "bootstrap"}', 'sistema', NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-02 18:05:14.670646-03');
INSERT INTO public.tb_logs_sistema VALUES (2, 'INFO', 'UsuÃ¡rio autenticado', '{"ip": "127.0.0.1", "usuario": "admin"}', 'auth', NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-02 18:05:14.670646-03');
INSERT INTO public.tb_logs_sistema VALUES (3, 'INFO', 'Rotina ETL executada', '{"rotina": "ImportacaoVendas", "registros": 1250}', 'etl', NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-02 18:05:14.670646-03');
INSERT INTO public.tb_logs_sistema VALUES (4, 'WARNING', 'Tentativa de conexÃ£o com credenciais invÃ¡lidas', '{"ip": "192.168.1.100"}', 'auth', NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-02 18:05:14.670646-03');
INSERT INTO public.tb_logs_sistema VALUES (5, 'ERROR', 'Falha ao conectar com banco de dados de origem', '{"erro": "Connection timeout", "host": "db.example.com"}', 'conexao', NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-02 18:05:14.670646-03');
INSERT INTO public.tb_logs_sistema VALUES (6, 'INFO', 'Agendamento criado', '{"cron": "0 2 * * *", "rotina_id": 5}', 'scheduler', NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-02 18:05:14.670646-03');
INSERT INTO public.tb_logs_sistema VALUES (7, 'WARNING', 'Rotina demorou mais que o esperado', '{"rotina": "ProcessamentoDiario", "tempo_ms": 125000}', 'etl', NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-02 18:05:14.670646-03');
INSERT INTO public.tb_logs_sistema VALUES (8, 'CRITICAL', 'Disco com pouco espaÃ§o', '{"disco": "C:", "espaco_livre": "5GB"}', 'sistema', NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-02 18:05:14.670646-03');
INSERT INTO public.tb_logs_sistema VALUES (9, 'DEBUG', 'Query SQL executada', '{"sql": "SELECT * FROM tb_rotinas", "tempo_ms": 45}', 'etl', NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-02 18:05:14.670646-03');
INSERT INTO public.tb_logs_sistema VALUES (10, 'INFO', 'Backup criado com sucesso', '{"arquivo": "backup_20260202.sql", "tamanho": "250MB"}', 'sistema', NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-02 18:05:14.670646-03');


--
-- TOC entry 5077 (class 0 OID 45647)
-- Dependencies: 240
-- Data for Name: tb_metricas_sistema; Type: TABLE DATA; Schema: public; Owner: postgres
--



--
-- TOC entry 5067 (class 0 OID 45561)
-- Dependencies: 230
-- Data for Name: tb_perfis_conexao; Type: TABLE DATA; Schema: public; Owner: postgres
--



--
-- TOC entry 5069 (class 0 OID 45572)
-- Dependencies: 232
-- Data for Name: tb_rotinas; Type: TABLE DATA; Schema: public; Owner: postgres
--



--
-- TOC entry 5065 (class 0 OID 45550)
-- Dependencies: 228
-- Data for Name: tb_usuarios; Type: TABLE DATA; Schema: public; Owner: postgres
--

INSERT INTO public.tb_usuarios OVERRIDING SYSTEM VALUE VALUES (1, 'admin', '$2y$10$LikSAYU.brSi1ILxdi8LyuTkScnB.bz6we1gxc68fo40thOErlkY.', false, 'admin', '2026-02-02 11:08:48.320843-03', false);


--
-- TOC entry 5079 (class 0 OID 45658)
-- Dependencies: 242
-- Data for Name: tb_worker_heartbeat; Type: TABLE DATA; Schema: public; Owner: postgres
--



--
-- TOC entry 5059 (class 0 OID 45509)
-- Dependencies: 222
-- Data for Name: users; Type: TABLE DATA; Schema: public; Owner: postgres
--

INSERT INTO public.users VALUES (1, 'admin', '$pbkdf2-sha256$29000$IwSglFKKMQaAUGrNmdMaow$keSRXb1L7fTWCcKQ7MLXfHGSaBRZbxy9qQmUueCR.ww');


--
-- TOC entry 5103 (class 0 OID 0)
-- Dependencies: 223
-- Name: connections_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.connections_id_seq', 1, true);


--
-- TOC entry 5104 (class 0 OID 0)
-- Dependencies: 244
-- Name: logs_sistema_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.logs_sistema_id_seq', 8, true);


--
-- TOC entry 5105 (class 0 OID 0)
-- Dependencies: 225
-- Name: schedules_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.schedules_id_seq', 1, true);


--
-- TOC entry 5106 (class 0 OID 0)
-- Dependencies: 217
-- Name: tb_arquivos_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.tb_arquivos_id_seq', 3, true);


--
-- TOC entry 5107 (class 0 OID 0)
-- Dependencies: 219
-- Name: tb_auditoria_rotina_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.tb_auditoria_rotina_id_seq', 5, true);


--
-- TOC entry 5108 (class 0 OID 0)
-- Dependencies: 233
-- Name: tb_blocos_rotina_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.tb_blocos_rotina_id_seq', 39, true);


--
-- TOC entry 5109 (class 0 OID 0)
-- Dependencies: 235
-- Name: tb_logs_execucao_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.tb_logs_execucao_id_seq', 17, true);


--
-- TOC entry 5110 (class 0 OID 0)
-- Dependencies: 237
-- Name: tb_logs_sistema_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.tb_logs_sistema_id_seq', 10, true);


--
-- TOC entry 5111 (class 0 OID 0)
-- Dependencies: 239
-- Name: tb_metricas_sistema_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.tb_metricas_sistema_id_seq', 1, false);


--
-- TOC entry 5112 (class 0 OID 0)
-- Dependencies: 229
-- Name: tb_perfis_conexao_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.tb_perfis_conexao_id_seq', 10, true);


--
-- TOC entry 5113 (class 0 OID 0)
-- Dependencies: 231
-- Name: tb_rotinas_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.tb_rotinas_id_seq', 24, true);


--
-- TOC entry 5114 (class 0 OID 0)
-- Dependencies: 227
-- Name: tb_usuarios_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.tb_usuarios_id_seq', 2, true);


--
-- TOC entry 5115 (class 0 OID 0)
-- Dependencies: 241
-- Name: tb_worker_heartbeat_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.tb_worker_heartbeat_id_seq', 1, false);


--
-- TOC entry 5116 (class 0 OID 0)
-- Dependencies: 221
-- Name: users_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.users_id_seq', 1, true);


--
-- TOC entry 4864 (class 2606 OID 45527)
-- Name: connections connections_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.connections
    ADD CONSTRAINT connections_pkey PRIMARY KEY (id);


--
-- TOC entry 4901 (class 2606 OID 45686)
-- Name: logs_sistema logs_sistema_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.logs_sistema
    ADD CONSTRAINT logs_sistema_pkey PRIMARY KEY (id);


--
-- TOC entry 4866 (class 2606 OID 45536)
-- Name: schedules schedules_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.schedules
    ADD CONSTRAINT schedules_pkey PRIMARY KEY (id);


--
-- TOC entry 4856 (class 2606 OID 45493)
-- Name: tb_arquivos tb_arquivos_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tb_arquivos
    ADD CONSTRAINT tb_arquivos_pkey PRIMARY KEY (id);


--
-- TOC entry 4858 (class 2606 OID 45502)
-- Name: tb_auditoria_rotina tb_auditoria_rotina_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tb_auditoria_rotina
    ADD CONSTRAINT tb_auditoria_rotina_pkey PRIMARY KEY (id);


--
-- TOC entry 4879 (class 2606 OID 45600)
-- Name: tb_blocos_rotina tb_blocos_rotina_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tb_blocos_rotina
    ADD CONSTRAINT tb_blocos_rotina_pkey PRIMARY KEY (id);


--
-- TOC entry 4884 (class 2606 OID 45614)
-- Name: tb_logs_execucao tb_logs_execucao_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tb_logs_execucao
    ADD CONSTRAINT tb_logs_execucao_pkey PRIMARY KEY (id);


--
-- TOC entry 4889 (class 2606 OID 45642)
-- Name: tb_logs_sistema tb_logs_sistema_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tb_logs_sistema
    ADD CONSTRAINT tb_logs_sistema_pkey PRIMARY KEY (id);


--
-- TOC entry 4891 (class 2606 OID 45656)
-- Name: tb_metricas_sistema tb_metricas_sistema_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tb_metricas_sistema
    ADD CONSTRAINT tb_metricas_sistema_pkey PRIMARY KEY (id);


--
-- TOC entry 4870 (class 2606 OID 45568)
-- Name: tb_perfis_conexao tb_perfis_conexao_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tb_perfis_conexao
    ADD CONSTRAINT tb_perfis_conexao_pkey PRIMARY KEY (id);


--
-- TOC entry 4877 (class 2606 OID 45580)
-- Name: tb_rotinas tb_rotinas_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tb_rotinas
    ADD CONSTRAINT tb_rotinas_pkey PRIMARY KEY (id);


--
-- TOC entry 4868 (class 2606 OID 45559)
-- Name: tb_usuarios tb_usuarios_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tb_usuarios
    ADD CONSTRAINT tb_usuarios_pkey PRIMARY KEY (id);


--
-- TOC entry 4895 (class 2606 OID 45667)
-- Name: tb_worker_heartbeat tb_worker_heartbeat_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tb_worker_heartbeat
    ADD CONSTRAINT tb_worker_heartbeat_pkey PRIMARY KEY (id);


--
-- TOC entry 4897 (class 2606 OID 45669)
-- Name: tb_worker_heartbeat tb_worker_heartbeat_worker_id_key; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tb_worker_heartbeat
    ADD CONSTRAINT tb_worker_heartbeat_worker_id_key UNIQUE (worker_id);


--
-- TOC entry 4872 (class 2606 OID 45570)
-- Name: tb_perfis_conexao uq_tb_perfis_conexao_nome; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tb_perfis_conexao
    ADD CONSTRAINT uq_tb_perfis_conexao_nome UNIQUE (nome_conexao);


--
-- TOC entry 4860 (class 2606 OID 45516)
-- Name: users users_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_pkey PRIMARY KEY (id);


--
-- TOC entry 4862 (class 2606 OID 45518)
-- Name: users users_username_key; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_username_key UNIQUE (username);


--
-- TOC entry 4892 (class 1259 OID 45670)
-- Name: idx_heartbeat_status; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_heartbeat_status ON public.tb_worker_heartbeat USING btree (status);


--
-- TOC entry 4893 (class 1259 OID 45671)
-- Name: idx_heartbeat_ultimo; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_heartbeat_ultimo ON public.tb_worker_heartbeat USING btree (ultimo_heartbeat DESC);


--
-- TOC entry 4885 (class 1259 OID 45645)
-- Name: idx_logs_canal; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_logs_canal ON public.tb_logs_sistema USING btree (canal);


--
-- TOC entry 4898 (class 1259 OID 45692)
-- Name: idx_logs_categoria; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_logs_categoria ON public.logs_sistema USING btree (categoria);


--
-- TOC entry 4899 (class 1259 OID 45693)
-- Name: idx_logs_created_at; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_logs_created_at ON public.logs_sistema USING btree (created_at);


--
-- TOC entry 4886 (class 1259 OID 45644)
-- Name: idx_logs_criado_em; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_logs_criado_em ON public.tb_logs_sistema USING btree (criado_em DESC);


--
-- TOC entry 4880 (class 1259 OID 45629)
-- Name: idx_logs_data_inicio; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_logs_data_inicio ON public.tb_logs_execucao USING btree (data_inicio DESC);


--
-- TOC entry 4887 (class 1259 OID 45643)
-- Name: idx_logs_nivel; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_logs_nivel ON public.tb_logs_sistema USING btree (nivel);


--
-- TOC entry 4881 (class 1259 OID 45630)
-- Name: idx_logs_rotina_data; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_logs_rotina_data ON public.tb_logs_execucao USING btree (id_rotina, data_inicio DESC);


--
-- TOC entry 4882 (class 1259 OID 45628)
-- Name: idx_logs_status; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_logs_status ON public.tb_logs_execucao USING btree (status);


--
-- TOC entry 4873 (class 1259 OID 45627)
-- Name: idx_rotinas_ativa_proxima; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_rotinas_ativa_proxima ON public.tb_rotinas USING btree (ativa, proxima_execucao) WHERE (ativa = true);


--
-- TOC entry 4874 (class 1259 OID 45709)
-- Name: idx_rotinas_datas_ignorar; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_rotinas_datas_ignorar ON public.tb_rotinas USING gin (datas_ignorar_json);


--
-- TOC entry 4875 (class 1259 OID 45708)
-- Name: idx_rotinas_periodo_agendamento; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_rotinas_periodo_agendamento ON public.tb_rotinas USING btree (data_inicio, data_fim) WHERE (agendamento_cron IS NOT NULL);


--
-- TOC entry 4907 (class 2606 OID 45687)
-- Name: logs_sistema fk_logs_usuario; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.logs_sistema
    ADD CONSTRAINT fk_logs_usuario FOREIGN KEY (usuario_id) REFERENCES public.tb_usuarios(id) ON DELETE SET NULL;


--
-- TOC entry 4902 (class 2606 OID 45503)
-- Name: tb_auditoria_rotina tb_auditoria_rotina_id_arquivo_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tb_auditoria_rotina
    ADD CONSTRAINT tb_auditoria_rotina_id_arquivo_fkey FOREIGN KEY (id_arquivo) REFERENCES public.tb_arquivos(id);


--
-- TOC entry 4905 (class 2606 OID 45601)
-- Name: tb_blocos_rotina tb_blocos_rotina_id_rotina_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tb_blocos_rotina
    ADD CONSTRAINT tb_blocos_rotina_id_rotina_fkey FOREIGN KEY (id_rotina) REFERENCES public.tb_rotinas(id) ON DELETE CASCADE;


--
-- TOC entry 4906 (class 2606 OID 45615)
-- Name: tb_logs_execucao tb_logs_execucao_id_rotina_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tb_logs_execucao
    ADD CONSTRAINT tb_logs_execucao_id_rotina_fkey FOREIGN KEY (id_rotina) REFERENCES public.tb_rotinas(id) ON DELETE SET NULL;


--
-- TOC entry 4903 (class 2606 OID 45581)
-- Name: tb_rotinas tb_rotinas_id_conexao_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tb_rotinas
    ADD CONSTRAINT tb_rotinas_id_conexao_fkey FOREIGN KEY (id_conexao) REFERENCES public.tb_perfis_conexao(id) ON DELETE RESTRICT;


--
-- TOC entry 4904 (class 2606 OID 45586)
-- Name: tb_rotinas tb_rotinas_id_usuario_criador_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tb_rotinas
    ADD CONSTRAINT tb_rotinas_id_usuario_criador_fkey FOREIGN KEY (id_usuario_criador) REFERENCES public.tb_usuarios(id) ON DELETE SET NULL;


-- Completed on 2026-02-02 20:02:16

--
-- PostgreSQL database dump complete
--

