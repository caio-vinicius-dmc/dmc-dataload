--
-- PostgreSQL database dump
--

-- Dumped from database version 17.5
-- Dumped by pg_dump version 17.5

-- Started on 2026-03-16 17:16:13

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
-- TOC entry 5281 (class 0 OID 0)
-- Dependencies: 4
-- Name: SCHEMA public; Type: COMMENT; Schema: -; Owner: pg_database_owner
--

COMMENT ON SCHEMA public IS 'standard public schema';


--
-- TOC entry 904 (class 1247 OID 45538)
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
-- TOC entry 222 (class 1259 OID 45520)
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
-- TOC entry 221 (class 1259 OID 45519)
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
-- TOC entry 5282 (class 0 OID 0)
-- Dependencies: 221
-- Name: connections_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.connections_id_seq OWNED BY public.connections.id;


--
-- TOC entry 243 (class 1259 OID 45677)
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
-- TOC entry 242 (class 1259 OID 45676)
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
-- TOC entry 5283 (class 0 OID 0)
-- Dependencies: 242
-- Name: logs_sistema_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.logs_sistema_id_seq OWNED BY public.logs_sistema.id;


--
-- TOC entry 224 (class 1259 OID 45529)
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
-- TOC entry 223 (class 1259 OID 45528)
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
-- TOC entry 5284 (class 0 OID 0)
-- Dependencies: 223
-- Name: schedules_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.schedules_id_seq OWNED BY public.schedules.id;


--
-- TOC entry 245 (class 1259 OID 45711)
-- Name: tb_api_externas; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.tb_api_externas (
    id integer NOT NULL,
    nome character varying(255) NOT NULL,
    descricao text,
    url text NOT NULL,
    metodo character varying(10) DEFAULT 'GET'::character varying,
    headers jsonb DEFAULT '{}'::jsonb,
    auth_tipo character varying(50) DEFAULT 'none'::character varying,
    credenciais jsonb DEFAULT '{}'::jsonb,
    body_template text,
    tipo_resposta character varying(20) DEFAULT 'json'::character varying,
    intervalo_polling integer DEFAULT 60,
    timeout integer DEFAULT 30,
    ativo boolean DEFAULT true,
    ultima_verificacao timestamp with time zone,
    ultimo_status character varying(50),
    ultimo_erro text,
    criado_por integer,
    data_criacao timestamp with time zone DEFAULT now(),
    data_atualizacao timestamp with time zone DEFAULT now()
);


ALTER TABLE public.tb_api_externas OWNER TO postgres;

--
-- TOC entry 5285 (class 0 OID 0)
-- Dependencies: 245
-- Name: TABLE tb_api_externas; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON TABLE public.tb_api_externas IS 'APIs externas cadastradas para monitoramento';


--
-- TOC entry 244 (class 1259 OID 45710)
-- Name: tb_api_externas_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.tb_api_externas_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.tb_api_externas_id_seq OWNER TO postgres;

--
-- TOC entry 5286 (class 0 OID 0)
-- Dependencies: 244
-- Name: tb_api_externas_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.tb_api_externas_id_seq OWNED BY public.tb_api_externas.id;


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
-- TOC entry 5287 (class 0 OID 0)
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
-- TOC entry 5288 (class 0 OID 0)
-- Dependencies: 219
-- Name: tb_auditoria_rotina_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.tb_auditoria_rotina_id_seq OWNED BY public.tb_auditoria_rotina.id;


--
-- TOC entry 232 (class 1259 OID 45592)
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
-- TOC entry 231 (class 1259 OID 45591)
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
-- TOC entry 247 (class 1259 OID 45732)
-- Name: tb_eventos_api; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.tb_eventos_api (
    id integer NOT NULL,
    id_api integer NOT NULL,
    nome character varying(255) NOT NULL,
    descricao text,
    jsonpath character varying(500),
    xpath character varying(500),
    tipo_valor character varying(20) DEFAULT 'string'::character varying,
    operador character varying(30) NOT NULL,
    valor_esperado text,
    acao character varying(50) DEFAULT 'trigger_workflow'::character varying,
    id_workflow integer,
    armazenar_valor boolean DEFAULT true,
    ativo boolean DEFAULT true,
    ultima_verificacao timestamp with time zone,
    ultimo_valor_capturado text,
    ultimo_match boolean,
    total_matches integer DEFAULT 0,
    data_criacao timestamp with time zone DEFAULT now(),
    data_atualizacao timestamp with time zone DEFAULT now()
);


ALTER TABLE public.tb_eventos_api OWNER TO postgres;

--
-- TOC entry 5289 (class 0 OID 0)
-- Dependencies: 247
-- Name: TABLE tb_eventos_api; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON TABLE public.tb_eventos_api IS 'Eventos/condições monitoradas em cada API';


--
-- TOC entry 246 (class 1259 OID 45731)
-- Name: tb_eventos_api_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.tb_eventos_api_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.tb_eventos_api_id_seq OWNER TO postgres;

--
-- TOC entry 5290 (class 0 OID 0)
-- Dependencies: 246
-- Name: tb_eventos_api_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.tb_eventos_api_id_seq OWNED BY public.tb_eventos_api.id;


--
-- TOC entry 234 (class 1259 OID 45607)
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
-- TOC entry 233 (class 1259 OID 45606)
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
-- TOC entry 236 (class 1259 OID 45632)
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
-- TOC entry 235 (class 1259 OID 45631)
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
-- TOC entry 5291 (class 0 OID 0)
-- Dependencies: 235
-- Name: tb_logs_sistema_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.tb_logs_sistema_id_seq OWNED BY public.tb_logs_sistema.id;


--
-- TOC entry 238 (class 1259 OID 45647)
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
-- TOC entry 237 (class 1259 OID 45646)
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
-- TOC entry 5292 (class 0 OID 0)
-- Dependencies: 237
-- Name: tb_metricas_sistema_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.tb_metricas_sistema_id_seq OWNED BY public.tb_metricas_sistema.id;


--
-- TOC entry 228 (class 1259 OID 45561)
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
-- TOC entry 227 (class 1259 OID 45560)
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
-- TOC entry 263 (class 1259 OID 46278)
-- Name: tb_pipeline_execucoes; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.tb_pipeline_execucoes (
    id integer NOT NULL,
    id_pipeline integer NOT NULL,
    status character varying(50) DEFAULT 'pending'::character varying,
    data_inicio timestamp with time zone DEFAULT now(),
    data_fim timestamp with time zone,
    duracao_ms integer,
    nodes_total integer DEFAULT 0,
    nodes_executados integer DEFAULT 0,
    nodes_sucesso integer DEFAULT 0,
    nodes_falha integer DEFAULT 0,
    resultado jsonb DEFAULT '{}'::jsonb,
    log_execucao jsonb DEFAULT '[]'::jsonb,
    erro text,
    executado_por integer
);


ALTER TABLE public.tb_pipeline_execucoes OWNER TO postgres;

--
-- TOC entry 262 (class 1259 OID 46277)
-- Name: tb_pipeline_execucoes_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.tb_pipeline_execucoes_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.tb_pipeline_execucoes_id_seq OWNER TO postgres;

--
-- TOC entry 5293 (class 0 OID 0)
-- Dependencies: 262
-- Name: tb_pipeline_execucoes_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.tb_pipeline_execucoes_id_seq OWNED BY public.tb_pipeline_execucoes.id;


--
-- TOC entry 261 (class 1259 OID 46258)
-- Name: tb_pipelines; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.tb_pipelines (
    id integer NOT NULL,
    nome character varying(255) NOT NULL,
    descricao text,
    modo character varying(20) DEFAULT 'nocode'::character varying,
    ativo boolean DEFAULT false,
    dados_flow jsonb DEFAULT '{"drawflow": {"Home": {"data": {}}}}'::jsonb,
    dados_code text DEFAULT ''::text,
    variaveis jsonb DEFAULT '{}'::jsonb,
    agendamento_cron text,
    trigger_tipo character varying(50) DEFAULT 'manual'::character varying,
    trigger_config jsonb DEFAULT '{}'::jsonb,
    versao integer DEFAULT 1,
    tags jsonb DEFAULT '[]'::jsonb,
    criado_por integer,
    data_criacao timestamp with time zone DEFAULT now(),
    data_atualizacao timestamp with time zone DEFAULT now()
);


ALTER TABLE public.tb_pipelines OWNER TO postgres;

--
-- TOC entry 260 (class 1259 OID 46257)
-- Name: tb_pipelines_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.tb_pipelines_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.tb_pipelines_id_seq OWNER TO postgres;

--
-- TOC entry 5294 (class 0 OID 0)
-- Dependencies: 260
-- Name: tb_pipelines_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.tb_pipelines_id_seq OWNED BY public.tb_pipelines.id;


--
-- TOC entry 230 (class 1259 OID 45572)
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
-- TOC entry 5295 (class 0 OID 0)
-- Dependencies: 230
-- Name: COLUMN tb_rotinas.data_inicio; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.tb_rotinas.data_inicio IS 'Data e hora de início do agendamento (quando começar a executar)';


--
-- TOC entry 5296 (class 0 OID 0)
-- Dependencies: 230
-- Name: COLUMN tb_rotinas.data_fim; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.tb_rotinas.data_fim IS 'Data e hora de término do agendamento (quando parar de executar)';


--
-- TOC entry 5297 (class 0 OID 0)
-- Dependencies: 230
-- Name: COLUMN tb_rotinas.datas_ignorar_json; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.tb_rotinas.datas_ignorar_json IS 'Array JSON com datas específicas para não executar';


--
-- TOC entry 5298 (class 0 OID 0)
-- Dependencies: 230
-- Name: COLUMN tb_rotinas.ignorar_feriados; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.tb_rotinas.ignorar_feriados IS 'Se deve ignorar feriados nacionais brasileiros';


--
-- TOC entry 5299 (class 0 OID 0)
-- Dependencies: 230
-- Name: COLUMN tb_rotinas.timeout; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.tb_rotinas.timeout IS 'Timeout máximo de execução em segundos (padrão: 300s = 5min)';


--
-- TOC entry 5300 (class 0 OID 0)
-- Dependencies: 230
-- Name: COLUMN tb_rotinas.notificar_falha; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.tb_rotinas.notificar_falha IS 'Se deve notificar quando houver falha na execução';


--
-- TOC entry 229 (class 1259 OID 45571)
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
-- TOC entry 226 (class 1259 OID 45550)
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
-- TOC entry 225 (class 1259 OID 45549)
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
-- TOC entry 249 (class 1259 OID 45756)
-- Name: tb_valores_capturados; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.tb_valores_capturados (
    id integer NOT NULL,
    id_evento integer NOT NULL,
    id_api integer NOT NULL,
    valor text,
    valor_json jsonb,
    response_completo jsonb,
    "condição_match" boolean DEFAULT false,
    processado boolean DEFAULT false,
    id_workflow_execucao integer,
    data_captura timestamp with time zone DEFAULT now()
);


ALTER TABLE public.tb_valores_capturados OWNER TO postgres;

--
-- TOC entry 5301 (class 0 OID 0)
-- Dependencies: 249
-- Name: TABLE tb_valores_capturados; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON TABLE public.tb_valores_capturados IS 'Histórico de valores capturados das APIs';


--
-- TOC entry 248 (class 1259 OID 45755)
-- Name: tb_valores_capturados_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.tb_valores_capturados_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.tb_valores_capturados_id_seq OWNER TO postgres;

--
-- TOC entry 5302 (class 0 OID 0)
-- Dependencies: 248
-- Name: tb_valores_capturados_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.tb_valores_capturados_id_seq OWNED BY public.tb_valores_capturados.id;


--
-- TOC entry 240 (class 1259 OID 45658)
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
-- TOC entry 239 (class 1259 OID 45657)
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
-- TOC entry 5303 (class 0 OID 0)
-- Dependencies: 239
-- Name: tb_worker_heartbeat_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.tb_worker_heartbeat_id_seq OWNED BY public.tb_worker_heartbeat.id;


--
-- TOC entry 255 (class 1259 OID 45822)
-- Name: tb_workflow_edges; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.tb_workflow_edges (
    id integer NOT NULL,
    id_workflow integer NOT NULL,
    edge_id character varying(50) NOT NULL,
    node_origem character varying(50) NOT NULL,
    node_destino character varying(50) NOT NULL,
    condicao character varying(50) DEFAULT 'always'::character varying,
    expressao_condicional text,
    label character varying(100),
    estilo jsonb DEFAULT '{}'::jsonb,
    data_criacao timestamp with time zone DEFAULT now()
);


ALTER TABLE public.tb_workflow_edges OWNER TO postgres;

--
-- TOC entry 5304 (class 0 OID 0)
-- Dependencies: 255
-- Name: TABLE tb_workflow_edges; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON TABLE public.tb_workflow_edges IS 'Conexões entre nós do workflow';


--
-- TOC entry 254 (class 1259 OID 45821)
-- Name: tb_workflow_edges_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.tb_workflow_edges_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.tb_workflow_edges_id_seq OWNER TO postgres;

--
-- TOC entry 5305 (class 0 OID 0)
-- Dependencies: 254
-- Name: tb_workflow_edges_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.tb_workflow_edges_id_seq OWNED BY public.tb_workflow_edges.id;


--
-- TOC entry 257 (class 1259 OID 45844)
-- Name: tb_workflow_execucoes; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.tb_workflow_execucoes (
    id integer NOT NULL,
    id_workflow integer NOT NULL,
    versao_workflow integer DEFAULT 1,
    status character varying(50) DEFAULT 'pending'::character varying,
    triggered_by character varying(50),
    trigger_data jsonb DEFAULT '{}'::jsonb,
    contexto jsonb DEFAULT '{}'::jsonb,
    data_inicio timestamp with time zone DEFAULT now(),
    data_fim timestamp with time zone,
    duracao_ms integer,
    nodes_total integer DEFAULT 0,
    nodes_executados integer DEFAULT 0,
    nodes_sucesso integer DEFAULT 0,
    nodes_falha integer DEFAULT 0,
    nodes_pulados integer DEFAULT 0,
    resultado_json jsonb DEFAULT '{}'::jsonb,
    erro text,
    criado_por integer
);


ALTER TABLE public.tb_workflow_execucoes OWNER TO postgres;

--
-- TOC entry 5306 (class 0 OID 0)
-- Dependencies: 257
-- Name: TABLE tb_workflow_execucoes; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON TABLE public.tb_workflow_execucoes IS 'Histórico de execuções de workflows';


--
-- TOC entry 256 (class 1259 OID 45843)
-- Name: tb_workflow_execucoes_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.tb_workflow_execucoes_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.tb_workflow_execucoes_id_seq OWNER TO postgres;

--
-- TOC entry 5307 (class 0 OID 0)
-- Dependencies: 256
-- Name: tb_workflow_execucoes_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.tb_workflow_execucoes_id_seq OWNED BY public.tb_workflow_execucoes.id;


--
-- TOC entry 259 (class 1259 OID 45872)
-- Name: tb_workflow_node_execucoes; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.tb_workflow_node_execucoes (
    id integer NOT NULL,
    id_workflow_execucao integer NOT NULL,
    node_id character varying(50) NOT NULL,
    tipo_node character varying(50),
    label character varying(255),
    status character varying(50) DEFAULT 'pending'::character varying,
    data_inicio timestamp with time zone,
    data_fim timestamp with time zone,
    duracao_ms integer,
    input_data jsonb DEFAULT '{}'::jsonb,
    output_data jsonb DEFAULT '{}'::jsonb,
    erro text,
    ordem integer DEFAULT 0,
    tentativas integer DEFAULT 0
);


ALTER TABLE public.tb_workflow_node_execucoes OWNER TO postgres;

--
-- TOC entry 5308 (class 0 OID 0)
-- Dependencies: 259
-- Name: TABLE tb_workflow_node_execucoes; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON TABLE public.tb_workflow_node_execucoes IS 'Histórico de execução de cada nó';


--
-- TOC entry 258 (class 1259 OID 45871)
-- Name: tb_workflow_node_execucoes_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.tb_workflow_node_execucoes_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.tb_workflow_node_execucoes_id_seq OWNER TO postgres;

--
-- TOC entry 5309 (class 0 OID 0)
-- Dependencies: 258
-- Name: tb_workflow_node_execucoes_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.tb_workflow_node_execucoes_id_seq OWNED BY public.tb_workflow_node_execucoes.id;


--
-- TOC entry 253 (class 1259 OID 45799)
-- Name: tb_workflow_nodes; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.tb_workflow_nodes (
    id integer NOT NULL,
    id_workflow integer NOT NULL,
    node_id character varying(50) NOT NULL,
    tipo_node character varying(50) NOT NULL,
    label character varying(255),
    id_referencia integer,
    posicao_x integer DEFAULT 0,
    posicao_y integer DEFAULT 0,
    config_json jsonb DEFAULT '{}'::jsonb,
    ordem_execucao integer DEFAULT 0,
    data_criacao timestamp with time zone DEFAULT now()
);


ALTER TABLE public.tb_workflow_nodes OWNER TO postgres;

--
-- TOC entry 5310 (class 0 OID 0)
-- Dependencies: 253
-- Name: TABLE tb_workflow_nodes; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON TABLE public.tb_workflow_nodes IS 'Nós individuais de cada workflow';


--
-- TOC entry 252 (class 1259 OID 45798)
-- Name: tb_workflow_nodes_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.tb_workflow_nodes_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.tb_workflow_nodes_id_seq OWNER TO postgres;

--
-- TOC entry 5311 (class 0 OID 0)
-- Dependencies: 252
-- Name: tb_workflow_nodes_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.tb_workflow_nodes_id_seq OWNED BY public.tb_workflow_nodes.id;


--
-- TOC entry 251 (class 1259 OID 45781)
-- Name: tb_workflows; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.tb_workflows (
    id integer NOT NULL,
    nome character varying(255) NOT NULL,
    descricao text,
    ativo boolean DEFAULT false,
    dados_json jsonb DEFAULT '{"edges": [], "nodes": []}'::jsonb NOT NULL,
    versao integer DEFAULT 1,
    trigger_tipo character varying(50) DEFAULT 'manual'::character varying,
    trigger_config jsonb DEFAULT '{}'::jsonb,
    criado_por integer,
    data_criacao timestamp with time zone DEFAULT now(),
    data_atualizacao timestamp with time zone DEFAULT now()
);


ALTER TABLE public.tb_workflows OWNER TO postgres;

--
-- TOC entry 5312 (class 0 OID 0)
-- Dependencies: 251
-- Name: TABLE tb_workflows; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON TABLE public.tb_workflows IS 'Workflows de automação (fluxos)';


--
-- TOC entry 250 (class 1259 OID 45780)
-- Name: tb_workflows_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.tb_workflows_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.tb_workflows_id_seq OWNER TO postgres;

--
-- TOC entry 5313 (class 0 OID 0)
-- Dependencies: 250
-- Name: tb_workflows_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.tb_workflows_id_seq OWNED BY public.tb_workflows.id;


--
-- TOC entry 241 (class 1259 OID 45672)
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
-- TOC entry 4862 (class 2604 OID 45523)
-- Name: connections id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.connections ALTER COLUMN id SET DEFAULT nextval('public.connections_id_seq'::regclass);


--
-- TOC entry 4896 (class 2604 OID 45680)
-- Name: logs_sistema id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.logs_sistema ALTER COLUMN id SET DEFAULT nextval('public.logs_sistema_id_seq'::regclass);


--
-- TOC entry 4863 (class 2604 OID 45532)
-- Name: schedules id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.schedules ALTER COLUMN id SET DEFAULT nextval('public.schedules_id_seq'::regclass);


--
-- TOC entry 4899 (class 2604 OID 45714)
-- Name: tb_api_externas id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tb_api_externas ALTER COLUMN id SET DEFAULT nextval('public.tb_api_externas_id_seq'::regclass);


--
-- TOC entry 4859 (class 2604 OID 45488)
-- Name: tb_arquivos id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tb_arquivos ALTER COLUMN id SET DEFAULT nextval('public.tb_arquivos_id_seq'::regclass);


--
-- TOC entry 4861 (class 2604 OID 45498)
-- Name: tb_auditoria_rotina id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tb_auditoria_rotina ALTER COLUMN id SET DEFAULT nextval('public.tb_auditoria_rotina_id_seq'::regclass);


--
-- TOC entry 4910 (class 2604 OID 45735)
-- Name: tb_eventos_api id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tb_eventos_api ALTER COLUMN id SET DEFAULT nextval('public.tb_eventos_api_id_seq'::regclass);


--
-- TOC entry 4884 (class 2604 OID 45635)
-- Name: tb_logs_sistema id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tb_logs_sistema ALTER COLUMN id SET DEFAULT nextval('public.tb_logs_sistema_id_seq'::regclass);


--
-- TOC entry 4888 (class 2604 OID 45650)
-- Name: tb_metricas_sistema id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tb_metricas_sistema ALTER COLUMN id SET DEFAULT nextval('public.tb_metricas_sistema_id_seq'::regclass);


--
-- TOC entry 4970 (class 2604 OID 46281)
-- Name: tb_pipeline_execucoes id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tb_pipeline_execucoes ALTER COLUMN id SET DEFAULT nextval('public.tb_pipeline_execucoes_id_seq'::regclass);


--
-- TOC entry 4958 (class 2604 OID 46261)
-- Name: tb_pipelines id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tb_pipelines ALTER COLUMN id SET DEFAULT nextval('public.tb_pipelines_id_seq'::regclass);


--
-- TOC entry 4918 (class 2604 OID 45759)
-- Name: tb_valores_capturados id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tb_valores_capturados ALTER COLUMN id SET DEFAULT nextval('public.tb_valores_capturados_id_seq'::regclass);


--
-- TOC entry 4891 (class 2604 OID 45661)
-- Name: tb_worker_heartbeat id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tb_worker_heartbeat ALTER COLUMN id SET DEFAULT nextval('public.tb_worker_heartbeat_id_seq'::regclass);


--
-- TOC entry 4936 (class 2604 OID 45825)
-- Name: tb_workflow_edges id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tb_workflow_edges ALTER COLUMN id SET DEFAULT nextval('public.tb_workflow_edges_id_seq'::regclass);


--
-- TOC entry 4940 (class 2604 OID 45847)
-- Name: tb_workflow_execucoes id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tb_workflow_execucoes ALTER COLUMN id SET DEFAULT nextval('public.tb_workflow_execucoes_id_seq'::regclass);


--
-- TOC entry 4952 (class 2604 OID 45875)
-- Name: tb_workflow_node_execucoes id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tb_workflow_node_execucoes ALTER COLUMN id SET DEFAULT nextval('public.tb_workflow_node_execucoes_id_seq'::regclass);


--
-- TOC entry 4930 (class 2604 OID 45802)
-- Name: tb_workflow_nodes id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tb_workflow_nodes ALTER COLUMN id SET DEFAULT nextval('public.tb_workflow_nodes_id_seq'::regclass);


--
-- TOC entry 4922 (class 2604 OID 45784)
-- Name: tb_workflows id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tb_workflows ALTER COLUMN id SET DEFAULT nextval('public.tb_workflows_id_seq'::regclass);


--
-- TOC entry 5235 (class 0 OID 45520)
-- Dependencies: 222
-- Data for Name: connections; Type: TABLE DATA; Schema: public; Owner: postgres
--

INSERT INTO public.connections VALUES (1, 'local_sqlite', 'sqlite', NULL, '', 'C:\Users\caio.barros\OneDrive\Cloud\PESSOAL\CAIO\NOTEBOOK\PROJETOS\DMC-DATALOAD\backend\test_target.db', NULL, NULL, '', '{"driver": "sqlite", "database": "C:\\Users\\caio.barros\\OneDrive\\Cloud\\PESSOAL\\CAIO\\NOTEBOOK\\PROJETOS\\DMC-DATALOAD\\backend\\test_target.db"}');


--
-- TOC entry 5255 (class 0 OID 45677)
-- Dependencies: 243
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
INSERT INTO public.logs_sistema VALUES (9, 'info', 'scheduler', 'Agendamento configurado para rotina ID 25: */5 * * * *', NULL, 1, NULL, '2026-02-03 08:45:41.732583');
INSERT INTO public.logs_sistema VALUES (10, 'info', 'scheduler', 'Agendamento configurado para rotina ID 25: */5 * * * *', NULL, 1, NULL, '2026-02-03 10:25:03.608532');
INSERT INTO public.logs_sistema VALUES (11, 'info', 'scheduler', 'Scheduler Worker iniciado (PID: 13300)', NULL, NULL, NULL, '2026-02-03 10:25:17.791243');
INSERT INTO public.logs_sistema VALUES (12, 'debug', 'scheduler', 'Verificando rotinas agendadas...', NULL, NULL, NULL, '2026-02-03 10:25:17.794347');
INSERT INTO public.logs_sistema VALUES (13, 'error', 'scheduler', 'Erro na verificação: SQLSTATE[42P01]: Undefined table: 7 ERRO:  relação "rotinas" não existe
LINE 8:                     FROM rotinas r
                                 ^', NULL, NULL, NULL, '2026-02-03 10:25:17.797765');
INSERT INTO public.logs_sistema VALUES (14, 'info', 'scheduler', 'Scheduler iniciado', NULL, 1, NULL, '2026-02-03 10:25:18.13707');
INSERT INTO public.logs_sistema VALUES (15, 'debug', 'scheduler', 'Verificando rotinas agendadas...', NULL, NULL, NULL, '2026-02-03 10:26:17.878715');
INSERT INTO public.logs_sistema VALUES (16, 'error', 'scheduler', 'Erro na verificação: SQLSTATE[42P01]: Undefined table: 7 ERRO:  relação "rotinas" não existe
LINE 8:                     FROM rotinas r
                                 ^', NULL, NULL, NULL, '2026-02-03 10:26:17.880805');
INSERT INTO public.logs_sistema VALUES (17, 'debug', 'scheduler', 'Verificando rotinas agendadas...', NULL, NULL, NULL, '2026-02-03 10:27:17.965506');
INSERT INTO public.logs_sistema VALUES (18, 'error', 'scheduler', 'Erro na verificação: SQLSTATE[42P01]: Undefined table: 7 ERRO:  relação "rotinas" não existe
LINE 8:                     FROM rotinas r
                                 ^', NULL, NULL, NULL, '2026-02-03 10:27:17.967671');
INSERT INTO public.logs_sistema VALUES (19, 'debug', 'scheduler', 'Verificando rotinas agendadas...', NULL, NULL, NULL, '2026-02-03 10:28:18.042553');
INSERT INTO public.logs_sistema VALUES (20, 'error', 'scheduler', 'Erro na verificação: SQLSTATE[42P01]: Undefined table: 7 ERRO:  relação "rotinas" não existe
LINE 8:                     FROM rotinas r
                                 ^', NULL, NULL, NULL, '2026-02-03 10:28:18.044926');
INSERT INTO public.logs_sistema VALUES (21, 'debug', 'scheduler', 'Verificando rotinas agendadas...', NULL, NULL, NULL, '2026-02-03 10:29:18.114536');
INSERT INTO public.logs_sistema VALUES (22, 'error', 'scheduler', 'Erro na verificação: SQLSTATE[42P01]: Undefined table: 7 ERRO:  relação "rotinas" não existe
LINE 8:                     FROM rotinas r
                                 ^', NULL, NULL, NULL, '2026-02-03 10:29:18.117222');
INSERT INTO public.logs_sistema VALUES (23, 'debug', 'scheduler', 'Verificando rotinas agendadas...', NULL, NULL, NULL, '2026-02-03 10:30:18.202497');
INSERT INTO public.logs_sistema VALUES (24, 'error', 'scheduler', 'Erro na verificação: SQLSTATE[42P01]: Undefined table: 7 ERRO:  relação "rotinas" não existe
LINE 8:                     FROM rotinas r
                                 ^', NULL, NULL, NULL, '2026-02-03 10:30:18.204736');
INSERT INTO public.logs_sistema VALUES (25, 'debug', 'scheduler', 'Verificando rotinas agendadas...', NULL, NULL, NULL, '2026-02-03 10:31:18.267013');
INSERT INTO public.logs_sistema VALUES (26, 'error', 'scheduler', 'Erro na verificação: SQLSTATE[42P01]: Undefined table: 7 ERRO:  relação "rotinas" não existe
LINE 8:                     FROM rotinas r
                                 ^', NULL, NULL, NULL, '2026-02-03 10:31:18.270403');
INSERT INTO public.logs_sistema VALUES (27, 'debug', 'scheduler', 'Verificando rotinas agendadas...', NULL, NULL, NULL, '2026-02-03 10:32:18.368427');
INSERT INTO public.logs_sistema VALUES (28, 'error', 'scheduler', 'Erro na verificação: SQLSTATE[42P01]: Undefined table: 7 ERRO:  relação "rotinas" não existe
LINE 8:                     FROM rotinas r
                                 ^', NULL, NULL, NULL, '2026-02-03 10:32:18.372624');
INSERT INTO public.logs_sistema VALUES (29, 'debug', 'scheduler', 'Verificando rotinas agendadas...', NULL, NULL, NULL, '2026-02-03 10:33:18.477321');
INSERT INTO public.logs_sistema VALUES (30, 'error', 'scheduler', 'Erro na verificação: SQLSTATE[42P01]: Undefined table: 7 ERRO:  relação "rotinas" não existe
LINE 8:                     FROM rotinas r
                                 ^', NULL, NULL, NULL, '2026-02-03 10:33:18.512222');
INSERT INTO public.logs_sistema VALUES (31, 'debug', 'scheduler', 'Verificando rotinas agendadas...', NULL, NULL, NULL, '2026-02-03 10:34:18.628572');
INSERT INTO public.logs_sistema VALUES (32, 'error', 'scheduler', 'Erro na verificação: SQLSTATE[42P01]: Undefined table: 7 ERRO:  relação "rotinas" não existe
LINE 8:                     FROM rotinas r
                                 ^', NULL, NULL, NULL, '2026-02-03 10:34:18.631425');
INSERT INTO public.logs_sistema VALUES (33, 'debug', 'scheduler', 'Verificando rotinas agendadas...', NULL, NULL, NULL, '2026-02-03 10:35:18.702776');
INSERT INTO public.logs_sistema VALUES (34, 'error', 'scheduler', 'Erro na verificação: SQLSTATE[42P01]: Undefined table: 7 ERRO:  relação "rotinas" não existe
LINE 8:                     FROM rotinas r
                                 ^', NULL, NULL, NULL, '2026-02-03 10:35:18.737776');
INSERT INTO public.logs_sistema VALUES (35, 'debug', 'scheduler', 'Verificando rotinas agendadas...', NULL, NULL, NULL, '2026-02-03 10:36:18.799883');
INSERT INTO public.logs_sistema VALUES (36, 'error', 'scheduler', 'Erro na verificação: SQLSTATE[42P01]: Undefined table: 7 ERRO:  relação "rotinas" não existe
LINE 8:                     FROM rotinas r
                                 ^', NULL, NULL, NULL, '2026-02-03 10:36:18.802435');
INSERT INTO public.logs_sistema VALUES (37, 'debug', 'scheduler', 'Verificando rotinas agendadas...', NULL, NULL, NULL, '2026-02-03 10:37:18.8656');
INSERT INTO public.logs_sistema VALUES (38, 'error', 'scheduler', 'Erro na verificação: SQLSTATE[42P01]: Undefined table: 7 ERRO:  relação "rotinas" não existe
LINE 8:                     FROM rotinas r
                                 ^', NULL, NULL, NULL, '2026-02-03 10:37:18.869494');
INSERT INTO public.logs_sistema VALUES (39, 'debug', 'scheduler', 'Verificando rotinas agendadas...', NULL, NULL, NULL, '2026-02-03 10:38:18.94131');
INSERT INTO public.logs_sistema VALUES (40, 'error', 'scheduler', 'Erro na verificação: SQLSTATE[42P01]: Undefined table: 7 ERRO:  relação "rotinas" não existe
LINE 8:                     FROM rotinas r
                                 ^', NULL, NULL, NULL, '2026-02-03 10:38:18.944529');
INSERT INTO public.logs_sistema VALUES (41, 'debug', 'scheduler', 'Verificando rotinas agendadas...', NULL, NULL, NULL, '2026-02-03 10:39:19.06137');
INSERT INTO public.logs_sistema VALUES (42, 'error', 'scheduler', 'Erro na verificação: SQLSTATE[42P01]: Undefined table: 7 ERRO:  relação "rotinas" não existe
LINE 8:                     FROM rotinas r
                                 ^', NULL, NULL, NULL, '2026-02-03 10:39:19.070838');
INSERT INTO public.logs_sistema VALUES (43, 'debug', 'scheduler', 'Verificando rotinas agendadas...', NULL, NULL, NULL, '2026-02-03 10:40:19.157006');
INSERT INTO public.logs_sistema VALUES (44, 'error', 'scheduler', 'Erro na verificação: SQLSTATE[42P01]: Undefined table: 7 ERRO:  relação "rotinas" não existe
LINE 8:                     FROM rotinas r
                                 ^', NULL, NULL, NULL, '2026-02-03 10:40:19.159844');
INSERT INTO public.logs_sistema VALUES (45, 'debug', 'scheduler', 'Verificando rotinas agendadas...', NULL, NULL, NULL, '2026-02-03 10:41:19.251206');
INSERT INTO public.logs_sistema VALUES (46, 'error', 'scheduler', 'Erro na verificação: SQLSTATE[42P01]: Undefined table: 7 ERRO:  relação "rotinas" não existe
LINE 8:                     FROM rotinas r
                                 ^', NULL, NULL, NULL, '2026-02-03 10:41:19.254194');
INSERT INTO public.logs_sistema VALUES (47, 'debug', 'scheduler', 'Verificando rotinas agendadas...', NULL, NULL, NULL, '2026-02-03 10:42:19.376672');
INSERT INTO public.logs_sistema VALUES (48, 'error', 'scheduler', 'Erro na verificação: SQLSTATE[42P01]: Undefined table: 7 ERRO:  relação "rotinas" não existe
LINE 8:                     FROM rotinas r
                                 ^', NULL, NULL, NULL, '2026-02-03 10:42:19.378824');
INSERT INTO public.logs_sistema VALUES (49, 'debug', 'scheduler', 'Verificando rotinas agendadas...', NULL, NULL, NULL, '2026-02-03 10:43:19.475464');
INSERT INTO public.logs_sistema VALUES (50, 'error', 'scheduler', 'Erro na verificação: SQLSTATE[42P01]: Undefined table: 7 ERRO:  relação "rotinas" não existe
LINE 8:                     FROM rotinas r
                                 ^', NULL, NULL, NULL, '2026-02-03 10:43:19.479021');
INSERT INTO public.logs_sistema VALUES (51, 'debug', 'scheduler', 'Verificando rotinas agendadas...', NULL, NULL, NULL, '2026-02-03 10:44:19.535348');
INSERT INTO public.logs_sistema VALUES (52, 'error', 'scheduler', 'Erro na verificação: SQLSTATE[42P01]: Undefined table: 7 ERRO:  relação "rotinas" não existe
LINE 8:                     FROM rotinas r
                                 ^', NULL, NULL, NULL, '2026-02-03 10:44:19.537491');
INSERT INTO public.logs_sistema VALUES (53, 'debug', 'scheduler', 'Verificando rotinas agendadas...', NULL, NULL, NULL, '2026-02-03 10:45:19.627306');
INSERT INTO public.logs_sistema VALUES (103, 'debug', 'scheduler', 'Verificando rotinas agendadas...', NULL, NULL, NULL, '2026-02-03 11:10:21.964301');
INSERT INTO public.logs_sistema VALUES (54, 'error', 'scheduler', 'Erro na verificação: SQLSTATE[42P01]: Undefined table: 7 ERRO:  relação "rotinas" não existe
LINE 8:                     FROM rotinas r
                                 ^', NULL, NULL, NULL, '2026-02-03 10:45:19.669375');
INSERT INTO public.logs_sistema VALUES (55, 'debug', 'scheduler', 'Verificando rotinas agendadas...', NULL, NULL, NULL, '2026-02-03 10:46:19.744109');
INSERT INTO public.logs_sistema VALUES (56, 'error', 'scheduler', 'Erro na verificação: SQLSTATE[42P01]: Undefined table: 7 ERRO:  relação "rotinas" não existe
LINE 8:                     FROM rotinas r
                                 ^', NULL, NULL, NULL, '2026-02-03 10:46:19.746355');
INSERT INTO public.logs_sistema VALUES (57, 'debug', 'scheduler', 'Verificando rotinas agendadas...', NULL, NULL, NULL, '2026-02-03 10:47:19.830436');
INSERT INTO public.logs_sistema VALUES (58, 'error', 'scheduler', 'Erro na verificação: SQLSTATE[42P01]: Undefined table: 7 ERRO:  relação "rotinas" não existe
LINE 8:                     FROM rotinas r
                                 ^', NULL, NULL, NULL, '2026-02-03 10:47:19.836393');
INSERT INTO public.logs_sistema VALUES (59, 'debug', 'scheduler', 'Verificando rotinas agendadas...', NULL, NULL, NULL, '2026-02-03 10:48:19.912977');
INSERT INTO public.logs_sistema VALUES (60, 'error', 'scheduler', 'Erro na verificação: SQLSTATE[42P01]: Undefined table: 7 ERRO:  relação "rotinas" não existe
LINE 8:                     FROM rotinas r
                                 ^', NULL, NULL, NULL, '2026-02-03 10:48:19.916472');
INSERT INTO public.logs_sistema VALUES (61, 'debug', 'scheduler', 'Verificando rotinas agendadas...', NULL, NULL, NULL, '2026-02-03 10:49:19.96563');
INSERT INTO public.logs_sistema VALUES (62, 'error', 'scheduler', 'Erro na verificação: SQLSTATE[42P01]: Undefined table: 7 ERRO:  relação "rotinas" não existe
LINE 8:                     FROM rotinas r
                                 ^', NULL, NULL, NULL, '2026-02-03 10:49:19.97072');
INSERT INTO public.logs_sistema VALUES (63, 'debug', 'scheduler', 'Verificando rotinas agendadas...', NULL, NULL, NULL, '2026-02-03 10:50:20.035987');
INSERT INTO public.logs_sistema VALUES (64, 'error', 'scheduler', 'Erro na verificação: SQLSTATE[42P01]: Undefined table: 7 ERRO:  relação "rotinas" não existe
LINE 8:                     FROM rotinas r
                                 ^', NULL, NULL, NULL, '2026-02-03 10:50:20.039961');
INSERT INTO public.logs_sistema VALUES (65, 'debug', 'scheduler', 'Verificando rotinas agendadas...', NULL, NULL, NULL, '2026-02-03 10:51:20.117296');
INSERT INTO public.logs_sistema VALUES (66, 'error', 'scheduler', 'Erro na verificação: SQLSTATE[42P01]: Undefined table: 7 ERRO:  relação "rotinas" não existe
LINE 8:                     FROM rotinas r
                                 ^', NULL, NULL, NULL, '2026-02-03 10:51:20.12002');
INSERT INTO public.logs_sistema VALUES (67, 'debug', 'scheduler', 'Verificando rotinas agendadas...', NULL, NULL, NULL, '2026-02-03 10:52:20.173835');
INSERT INTO public.logs_sistema VALUES (68, 'error', 'scheduler', 'Erro na verificação: SQLSTATE[42P01]: Undefined table: 7 ERRO:  relação "rotinas" não existe
LINE 8:                     FROM rotinas r
                                 ^', NULL, NULL, NULL, '2026-02-03 10:52:20.179786');
INSERT INTO public.logs_sistema VALUES (69, 'debug', 'scheduler', 'Verificando rotinas agendadas...', NULL, NULL, NULL, '2026-02-03 10:53:20.215603');
INSERT INTO public.logs_sistema VALUES (70, 'error', 'scheduler', 'Erro na verificação: SQLSTATE[42P01]: Undefined table: 7 ERRO:  relação "rotinas" não existe
LINE 8:                     FROM rotinas r
                                 ^', NULL, NULL, NULL, '2026-02-03 10:53:20.250452');
INSERT INTO public.logs_sistema VALUES (71, 'debug', 'scheduler', 'Verificando rotinas agendadas...', NULL, NULL, NULL, '2026-02-03 10:54:20.333475');
INSERT INTO public.logs_sistema VALUES (72, 'error', 'scheduler', 'Erro na verificação: SQLSTATE[42P01]: Undefined table: 7 ERRO:  relação "rotinas" não existe
LINE 8:                     FROM rotinas r
                                 ^', NULL, NULL, NULL, '2026-02-03 10:54:20.368025');
INSERT INTO public.logs_sistema VALUES (73, 'debug', 'scheduler', 'Verificando rotinas agendadas...', NULL, NULL, NULL, '2026-02-03 10:55:20.450777');
INSERT INTO public.logs_sistema VALUES (74, 'error', 'scheduler', 'Erro na verificação: SQLSTATE[42P01]: Undefined table: 7 ERRO:  relação "rotinas" não existe
LINE 8:                     FROM rotinas r
                                 ^', NULL, NULL, NULL, '2026-02-03 10:55:20.453403');
INSERT INTO public.logs_sistema VALUES (75, 'debug', 'scheduler', 'Verificando rotinas agendadas...', NULL, NULL, NULL, '2026-02-03 10:56:20.541842');
INSERT INTO public.logs_sistema VALUES (76, 'error', 'scheduler', 'Erro na verificação: SQLSTATE[42P01]: Undefined table: 7 ERRO:  relação "rotinas" não existe
LINE 8:                     FROM rotinas r
                                 ^', NULL, NULL, NULL, '2026-02-03 10:56:20.5455');
INSERT INTO public.logs_sistema VALUES (77, 'debug', 'scheduler', 'Verificando rotinas agendadas...', NULL, NULL, NULL, '2026-02-03 10:57:20.636989');
INSERT INTO public.logs_sistema VALUES (78, 'error', 'scheduler', 'Erro na verificação: SQLSTATE[42P01]: Undefined table: 7 ERRO:  relação "rotinas" não existe
LINE 8:                     FROM rotinas r
                                 ^', NULL, NULL, NULL, '2026-02-03 10:57:20.639395');
INSERT INTO public.logs_sistema VALUES (79, 'debug', 'scheduler', 'Verificando rotinas agendadas...', NULL, NULL, NULL, '2026-02-03 10:58:20.748976');
INSERT INTO public.logs_sistema VALUES (80, 'error', 'scheduler', 'Erro na verificação: SQLSTATE[42P01]: Undefined table: 7 ERRO:  relação "rotinas" não existe
LINE 8:                     FROM rotinas r
                                 ^', NULL, NULL, NULL, '2026-02-03 10:58:20.752654');
INSERT INTO public.logs_sistema VALUES (81, 'debug', 'scheduler', 'Verificando rotinas agendadas...', NULL, NULL, NULL, '2026-02-03 10:59:20.835047');
INSERT INTO public.logs_sistema VALUES (82, 'error', 'scheduler', 'Erro na verificação: SQLSTATE[42P01]: Undefined table: 7 ERRO:  relação "rotinas" não existe
LINE 8:                     FROM rotinas r
                                 ^', NULL, NULL, NULL, '2026-02-03 10:59:20.840211');
INSERT INTO public.logs_sistema VALUES (83, 'debug', 'scheduler', 'Verificando rotinas agendadas...', NULL, NULL, NULL, '2026-02-03 11:00:20.921259');
INSERT INTO public.logs_sistema VALUES (84, 'error', 'scheduler', 'Erro na verificação: SQLSTATE[42P01]: Undefined table: 7 ERRO:  relação "rotinas" não existe
LINE 8:                     FROM rotinas r
                                 ^', NULL, NULL, NULL, '2026-02-03 11:00:20.923611');
INSERT INTO public.logs_sistema VALUES (85, 'debug', 'scheduler', 'Verificando rotinas agendadas...', NULL, NULL, NULL, '2026-02-03 11:01:21.031884');
INSERT INTO public.logs_sistema VALUES (86, 'error', 'scheduler', 'Erro na verificação: SQLSTATE[42P01]: Undefined table: 7 ERRO:  relação "rotinas" não existe
LINE 8:                     FROM rotinas r
                                 ^', NULL, NULL, NULL, '2026-02-03 11:01:21.034289');
INSERT INTO public.logs_sistema VALUES (87, 'debug', 'scheduler', 'Verificando rotinas agendadas...', NULL, NULL, NULL, '2026-02-03 11:02:21.103613');
INSERT INTO public.logs_sistema VALUES (88, 'error', 'scheduler', 'Erro na verificação: SQLSTATE[42P01]: Undefined table: 7 ERRO:  relação "rotinas" não existe
LINE 8:                     FROM rotinas r
                                 ^', NULL, NULL, NULL, '2026-02-03 11:02:21.137581');
INSERT INTO public.logs_sistema VALUES (89, 'debug', 'scheduler', 'Verificando rotinas agendadas...', NULL, NULL, NULL, '2026-02-03 11:03:21.237791');
INSERT INTO public.logs_sistema VALUES (90, 'error', 'scheduler', 'Erro na verificação: SQLSTATE[42P01]: Undefined table: 7 ERRO:  relação "rotinas" não existe
LINE 8:                     FROM rotinas r
                                 ^', NULL, NULL, NULL, '2026-02-03 11:03:21.241576');
INSERT INTO public.logs_sistema VALUES (91, 'debug', 'scheduler', 'Verificando rotinas agendadas...', NULL, NULL, NULL, '2026-02-03 11:04:21.340505');
INSERT INTO public.logs_sistema VALUES (92, 'error', 'scheduler', 'Erro na verificação: SQLSTATE[42P01]: Undefined table: 7 ERRO:  relação "rotinas" não existe
LINE 8:                     FROM rotinas r
                                 ^', NULL, NULL, NULL, '2026-02-03 11:04:21.343168');
INSERT INTO public.logs_sistema VALUES (93, 'debug', 'scheduler', 'Verificando rotinas agendadas...', NULL, NULL, NULL, '2026-02-03 11:05:21.428131');
INSERT INTO public.logs_sistema VALUES (94, 'error', 'scheduler', 'Erro na verificação: SQLSTATE[42P01]: Undefined table: 7 ERRO:  relação "rotinas" não existe
LINE 8:                     FROM rotinas r
                                 ^', NULL, NULL, NULL, '2026-02-03 11:05:21.431341');
INSERT INTO public.logs_sistema VALUES (95, 'debug', 'scheduler', 'Verificando rotinas agendadas...', NULL, NULL, NULL, '2026-02-03 11:06:21.51678');
INSERT INTO public.logs_sistema VALUES (96, 'error', 'scheduler', 'Erro na verificação: SQLSTATE[42P01]: Undefined table: 7 ERRO:  relação "rotinas" não existe
LINE 8:                     FROM rotinas r
                                 ^', NULL, NULL, NULL, '2026-02-03 11:06:21.52243');
INSERT INTO public.logs_sistema VALUES (97, 'debug', 'scheduler', 'Verificando rotinas agendadas...', NULL, NULL, NULL, '2026-02-03 11:07:21.596711');
INSERT INTO public.logs_sistema VALUES (98, 'error', 'scheduler', 'Erro na verificação: SQLSTATE[42P01]: Undefined table: 7 ERRO:  relação "rotinas" não existe
LINE 8:                     FROM rotinas r
                                 ^', NULL, NULL, NULL, '2026-02-03 11:07:21.598816');
INSERT INTO public.logs_sistema VALUES (99, 'debug', 'scheduler', 'Verificando rotinas agendadas...', NULL, NULL, NULL, '2026-02-03 11:08:21.744336');
INSERT INTO public.logs_sistema VALUES (100, 'error', 'scheduler', 'Erro na verificação: SQLSTATE[42P01]: Undefined table: 7 ERRO:  relação "rotinas" não existe
LINE 8:                     FROM rotinas r
                                 ^', NULL, NULL, NULL, '2026-02-03 11:08:21.786416');
INSERT INTO public.logs_sistema VALUES (101, 'debug', 'scheduler', 'Verificando rotinas agendadas...', NULL, NULL, NULL, '2026-02-03 11:09:21.856914');
INSERT INTO public.logs_sistema VALUES (102, 'error', 'scheduler', 'Erro na verificação: SQLSTATE[42P01]: Undefined table: 7 ERRO:  relação "rotinas" não existe
LINE 8:                     FROM rotinas r
                                 ^', NULL, NULL, NULL, '2026-02-03 11:09:21.859326');
INSERT INTO public.logs_sistema VALUES (104, 'error', 'scheduler', 'Erro na verificação: SQLSTATE[42P01]: Undefined table: 7 ERRO:  relação "rotinas" não existe
LINE 8:                     FROM rotinas r
                                 ^', NULL, NULL, NULL, '2026-02-03 11:10:21.968443');
INSERT INTO public.logs_sistema VALUES (105, 'debug', 'scheduler', 'Verificando rotinas agendadas...', NULL, NULL, NULL, '2026-02-03 11:11:22.072584');
INSERT INTO public.logs_sistema VALUES (106, 'error', 'scheduler', 'Erro na verificação: SQLSTATE[42P01]: Undefined table: 7 ERRO:  relação "rotinas" não existe
LINE 8:                     FROM rotinas r
                                 ^', NULL, NULL, NULL, '2026-02-03 11:11:22.078225');
INSERT INTO public.logs_sistema VALUES (107, 'debug', 'scheduler', 'Verificando rotinas agendadas...', NULL, NULL, NULL, '2026-02-03 11:12:22.140074');
INSERT INTO public.logs_sistema VALUES (108, 'error', 'scheduler', 'Erro na verificação: SQLSTATE[42P01]: Undefined table: 7 ERRO:  relação "rotinas" não existe
LINE 8:                     FROM rotinas r
                                 ^', NULL, NULL, NULL, '2026-02-03 11:12:22.215322');
INSERT INTO public.logs_sistema VALUES (109, 'debug', 'scheduler', 'Verificando rotinas agendadas...', NULL, NULL, NULL, '2026-02-03 11:13:22.320263');
INSERT INTO public.logs_sistema VALUES (110, 'error', 'scheduler', 'Erro na verificação: SQLSTATE[42P01]: Undefined table: 7 ERRO:  relação "rotinas" não existe
LINE 8:                     FROM rotinas r
                                 ^', NULL, NULL, NULL, '2026-02-03 11:13:22.324188');
INSERT INTO public.logs_sistema VALUES (111, 'debug', 'scheduler', 'Verificando rotinas agendadas...', NULL, NULL, NULL, '2026-02-03 11:14:22.409243');
INSERT INTO public.logs_sistema VALUES (112, 'error', 'scheduler', 'Erro na verificação: SQLSTATE[42P01]: Undefined table: 7 ERRO:  relação "rotinas" não existe
LINE 8:                     FROM rotinas r
                                 ^', NULL, NULL, NULL, '2026-02-03 11:14:22.413944');
INSERT INTO public.logs_sistema VALUES (113, 'debug', 'scheduler', 'Verificando rotinas agendadas...', NULL, NULL, NULL, '2026-02-03 11:15:22.487312');
INSERT INTO public.logs_sistema VALUES (114, 'error', 'scheduler', 'Erro na verificação: SQLSTATE[42P01]: Undefined table: 7 ERRO:  relação "rotinas" não existe
LINE 8:                     FROM rotinas r
                                 ^', NULL, NULL, NULL, '2026-02-03 11:15:22.522964');
INSERT INTO public.logs_sistema VALUES (115, 'debug', 'scheduler', 'Verificando rotinas agendadas...', NULL, NULL, NULL, '2026-02-03 11:16:22.630942');
INSERT INTO public.logs_sistema VALUES (116, 'error', 'scheduler', 'Erro na verificação: SQLSTATE[42P01]: Undefined table: 7 ERRO:  relação "rotinas" não existe
LINE 8:                     FROM rotinas r
                                 ^', NULL, NULL, NULL, '2026-02-03 11:16:22.635024');
INSERT INTO public.logs_sistema VALUES (117, 'debug', 'scheduler', 'Verificando rotinas agendadas...', NULL, NULL, NULL, '2026-02-03 11:17:22.707195');
INSERT INTO public.logs_sistema VALUES (118, 'error', 'scheduler', 'Erro na verificação: SQLSTATE[42P01]: Undefined table: 7 ERRO:  relação "rotinas" não existe
LINE 8:                     FROM rotinas r
                                 ^', NULL, NULL, NULL, '2026-02-03 11:17:22.742281');
INSERT INTO public.logs_sistema VALUES (119, 'debug', 'scheduler', 'Verificando rotinas agendadas...', NULL, NULL, NULL, '2026-02-03 11:18:22.834224');
INSERT INTO public.logs_sistema VALUES (120, 'error', 'scheduler', 'Erro na verificação: SQLSTATE[42P01]: Undefined table: 7 ERRO:  relação "rotinas" não existe
LINE 8:                     FROM rotinas r
                                 ^', NULL, NULL, NULL, '2026-02-03 11:18:22.868961');
INSERT INTO public.logs_sistema VALUES (121, 'debug', 'scheduler', 'Verificando rotinas agendadas...', NULL, NULL, NULL, '2026-02-03 11:19:22.965514');
INSERT INTO public.logs_sistema VALUES (122, 'error', 'scheduler', 'Erro na verificação: SQLSTATE[42P01]: Undefined table: 7 ERRO:  relação "rotinas" não existe
LINE 8:                     FROM rotinas r
                                 ^', NULL, NULL, NULL, '2026-02-03 11:19:22.971021');
INSERT INTO public.logs_sistema VALUES (123, 'debug', 'scheduler', 'Verificando rotinas agendadas...', NULL, NULL, NULL, '2026-02-03 11:20:23.115241');
INSERT INTO public.logs_sistema VALUES (124, 'error', 'scheduler', 'Erro na verificação: SQLSTATE[42P01]: Undefined table: 7 ERRO:  relação "rotinas" não existe
LINE 8:                     FROM rotinas r
                                 ^', NULL, NULL, NULL, '2026-02-03 11:20:23.118631');
INSERT INTO public.logs_sistema VALUES (125, 'debug', 'scheduler', 'Verificando rotinas agendadas...', NULL, NULL, NULL, '2026-02-03 11:21:23.230558');
INSERT INTO public.logs_sistema VALUES (126, 'error', 'scheduler', 'Erro na verificação: SQLSTATE[42P01]: Undefined table: 7 ERRO:  relação "rotinas" não existe
LINE 8:                     FROM rotinas r
                                 ^', NULL, NULL, NULL, '2026-02-03 11:21:23.232823');
INSERT INTO public.logs_sistema VALUES (127, 'debug', 'scheduler', 'Verificando rotinas agendadas...', NULL, NULL, NULL, '2026-02-03 11:22:23.302521');
INSERT INTO public.logs_sistema VALUES (128, 'error', 'scheduler', 'Erro na verificação: SQLSTATE[42P01]: Undefined table: 7 ERRO:  relação "rotinas" não existe
LINE 8:                     FROM rotinas r
                                 ^', NULL, NULL, NULL, '2026-02-03 11:22:23.30569');
INSERT INTO public.logs_sistema VALUES (129, 'debug', 'scheduler', 'Verificando rotinas agendadas...', NULL, NULL, NULL, '2026-02-03 11:23:23.441502');
INSERT INTO public.logs_sistema VALUES (130, 'error', 'scheduler', 'Erro na verificação: SQLSTATE[42P01]: Undefined table: 7 ERRO:  relação "rotinas" não existe
LINE 8:                     FROM rotinas r
                                 ^', NULL, NULL, NULL, '2026-02-03 11:23:23.447829');
INSERT INTO public.logs_sistema VALUES (131, 'debug', 'scheduler', 'Verificando rotinas agendadas...', NULL, NULL, NULL, '2026-02-03 11:24:23.484155');
INSERT INTO public.logs_sistema VALUES (132, 'error', 'scheduler', 'Erro na verificação: SQLSTATE[42P01]: Undefined table: 7 ERRO:  relação "rotinas" não existe
LINE 8:                     FROM rotinas r
                                 ^', NULL, NULL, NULL, '2026-02-03 11:24:23.524227');
INSERT INTO public.logs_sistema VALUES (133, 'debug', 'scheduler', 'Verificando rotinas agendadas...', NULL, NULL, NULL, '2026-02-03 11:25:23.622552');
INSERT INTO public.logs_sistema VALUES (134, 'error', 'scheduler', 'Erro na verificação: SQLSTATE[42P01]: Undefined table: 7 ERRO:  relação "rotinas" não existe
LINE 8:                     FROM rotinas r
                                 ^', NULL, NULL, NULL, '2026-02-03 11:25:23.63816');
INSERT INTO public.logs_sistema VALUES (135, 'debug', 'scheduler', 'Verificando rotinas agendadas...', NULL, NULL, NULL, '2026-02-03 11:26:23.748093');
INSERT INTO public.logs_sistema VALUES (136, 'error', 'scheduler', 'Erro na verificação: SQLSTATE[42P01]: Undefined table: 7 ERRO:  relação "rotinas" não existe
LINE 8:                     FROM rotinas r
                                 ^', NULL, NULL, NULL, '2026-02-03 11:26:23.779072');
INSERT INTO public.logs_sistema VALUES (137, 'debug', 'scheduler', 'Verificando rotinas agendadas...', NULL, NULL, NULL, '2026-02-03 11:27:23.881223');
INSERT INTO public.logs_sistema VALUES (138, 'error', 'scheduler', 'Erro na verificação: SQLSTATE[42P01]: Undefined table: 7 ERRO:  relação "rotinas" não existe
LINE 8:                     FROM rotinas r
                                 ^', NULL, NULL, NULL, '2026-02-03 11:27:23.88475');
INSERT INTO public.logs_sistema VALUES (139, 'debug', 'scheduler', 'Verificando rotinas agendadas...', NULL, NULL, NULL, '2026-02-03 11:28:23.985084');
INSERT INTO public.logs_sistema VALUES (140, 'error', 'scheduler', 'Erro na verificação: SQLSTATE[42P01]: Undefined table: 7 ERRO:  relação "rotinas" não existe
LINE 8:                     FROM rotinas r
                                 ^', NULL, NULL, NULL, '2026-02-03 11:28:23.990516');
INSERT INTO public.logs_sistema VALUES (141, 'debug', 'scheduler', 'Verificando rotinas agendadas...', NULL, NULL, NULL, '2026-02-03 11:29:24.053583');
INSERT INTO public.logs_sistema VALUES (142, 'error', 'scheduler', 'Erro na verificação: SQLSTATE[42P01]: Undefined table: 7 ERRO:  relação "rotinas" não existe
LINE 8:                     FROM rotinas r
                                 ^', NULL, NULL, NULL, '2026-02-03 11:29:24.089025');
INSERT INTO public.logs_sistema VALUES (143, 'debug', 'scheduler', 'Verificando rotinas agendadas...', NULL, NULL, NULL, '2026-02-03 11:30:24.126238');
INSERT INTO public.logs_sistema VALUES (144, 'error', 'scheduler', 'Erro na verificação: SQLSTATE[42P01]: Undefined table: 7 ERRO:  relação "rotinas" não existe
LINE 8:                     FROM rotinas r
                                 ^', NULL, NULL, NULL, '2026-02-03 11:30:24.16202');
INSERT INTO public.logs_sistema VALUES (145, 'debug', 'scheduler', 'Verificando rotinas agendadas...', NULL, NULL, NULL, '2026-02-03 11:31:24.25549');
INSERT INTO public.logs_sistema VALUES (146, 'error', 'scheduler', 'Erro na verificação: SQLSTATE[42P01]: Undefined table: 7 ERRO:  relação "rotinas" não existe
LINE 8:                     FROM rotinas r
                                 ^', NULL, NULL, NULL, '2026-02-03 11:31:24.259684');
INSERT INTO public.logs_sistema VALUES (147, 'debug', 'scheduler', 'Verificando rotinas agendadas...', NULL, NULL, NULL, '2026-02-03 11:32:24.346443');
INSERT INTO public.logs_sistema VALUES (148, 'error', 'scheduler', 'Erro na verificação: SQLSTATE[42P01]: Undefined table: 7 ERRO:  relação "rotinas" não existe
LINE 8:                     FROM rotinas r
                                 ^', NULL, NULL, NULL, '2026-02-03 11:32:24.387386');
INSERT INTO public.logs_sistema VALUES (149, 'debug', 'scheduler', 'Verificando rotinas agendadas...', NULL, NULL, NULL, '2026-02-03 11:33:24.527156');
INSERT INTO public.logs_sistema VALUES (150, 'error', 'scheduler', 'Erro na verificação: SQLSTATE[42P01]: Undefined table: 7 ERRO:  relação "rotinas" não existe
LINE 8:                     FROM rotinas r
                                 ^', NULL, NULL, NULL, '2026-02-03 11:33:24.534267');
INSERT INTO public.logs_sistema VALUES (151, 'debug', 'scheduler', 'Verificando rotinas agendadas...', NULL, NULL, NULL, '2026-02-03 11:34:24.627755');
INSERT INTO public.logs_sistema VALUES (152, 'error', 'scheduler', 'Erro na verificação: SQLSTATE[42P01]: Undefined table: 7 ERRO:  relação "rotinas" não existe
LINE 8:                     FROM rotinas r
                                 ^', NULL, NULL, NULL, '2026-02-03 11:34:24.636589');
INSERT INTO public.logs_sistema VALUES (153, 'debug', 'scheduler', 'Verificando rotinas agendadas...', NULL, NULL, NULL, '2026-02-03 11:35:24.726247');
INSERT INTO public.logs_sistema VALUES (154, 'error', 'scheduler', 'Erro na verificação: SQLSTATE[42P01]: Undefined table: 7 ERRO:  relação "rotinas" não existe
LINE 8:                     FROM rotinas r
                                 ^', NULL, NULL, NULL, '2026-02-03 11:35:24.740011');
INSERT INTO public.logs_sistema VALUES (155, 'debug', 'scheduler', 'Verificando rotinas agendadas...', NULL, NULL, NULL, '2026-02-03 11:36:24.8216');
INSERT INTO public.logs_sistema VALUES (156, 'error', 'scheduler', 'Erro na verificação: SQLSTATE[42P01]: Undefined table: 7 ERRO:  relação "rotinas" não existe
LINE 8:                     FROM rotinas r
                                 ^', NULL, NULL, NULL, '2026-02-03 11:36:24.82694');
INSERT INTO public.logs_sistema VALUES (157, 'debug', 'scheduler', 'Verificando rotinas agendadas...', NULL, NULL, NULL, '2026-02-03 11:37:24.896409');
INSERT INTO public.logs_sistema VALUES (158, 'error', 'scheduler', 'Erro na verificação: SQLSTATE[42P01]: Undefined table: 7 ERRO:  relação "rotinas" não existe
LINE 8:                     FROM rotinas r
                                 ^', NULL, NULL, NULL, '2026-02-03 11:37:24.901436');
INSERT INTO public.logs_sistema VALUES (159, 'debug', 'scheduler', 'Verificando rotinas agendadas...', NULL, NULL, NULL, '2026-02-03 11:38:24.986796');
INSERT INTO public.logs_sistema VALUES (160, 'error', 'scheduler', 'Erro na verificação: SQLSTATE[42P01]: Undefined table: 7 ERRO:  relação "rotinas" não existe
LINE 8:                     FROM rotinas r
                                 ^', NULL, NULL, NULL, '2026-02-03 11:38:24.993999');
INSERT INTO public.logs_sistema VALUES (161, 'debug', 'scheduler', 'Verificando rotinas agendadas...', NULL, NULL, NULL, '2026-02-03 11:39:25.075618');
INSERT INTO public.logs_sistema VALUES (162, 'error', 'scheduler', 'Erro na verificação: SQLSTATE[42P01]: Undefined table: 7 ERRO:  relação "rotinas" não existe
LINE 8:                     FROM rotinas r
                                 ^', NULL, NULL, NULL, '2026-02-03 11:39:25.113192');
INSERT INTO public.logs_sistema VALUES (163, 'debug', 'scheduler', 'Verificando rotinas agendadas...', NULL, NULL, NULL, '2026-02-03 11:40:25.197083');
INSERT INTO public.logs_sistema VALUES (164, 'error', 'scheduler', 'Erro na verificação: SQLSTATE[42P01]: Undefined table: 7 ERRO:  relação "rotinas" não existe
LINE 8:                     FROM rotinas r
                                 ^', NULL, NULL, NULL, '2026-02-03 11:40:25.219561');
INSERT INTO public.logs_sistema VALUES (165, 'debug', 'scheduler', 'Verificando rotinas agendadas...', NULL, NULL, NULL, '2026-02-03 11:41:25.307255');
INSERT INTO public.logs_sistema VALUES (166, 'error', 'scheduler', 'Erro na verificação: SQLSTATE[42P01]: Undefined table: 7 ERRO:  relação "rotinas" não existe
LINE 8:                     FROM rotinas r
                                 ^', NULL, NULL, NULL, '2026-02-03 11:41:25.311051');
INSERT INTO public.logs_sistema VALUES (167, 'debug', 'scheduler', 'Verificando rotinas agendadas...', NULL, NULL, NULL, '2026-02-03 11:42:25.450581');
INSERT INTO public.logs_sistema VALUES (168, 'error', 'scheduler', 'Erro na verificação: SQLSTATE[42P01]: Undefined table: 7 ERRO:  relação "rotinas" não existe
LINE 8:                     FROM rotinas r
                                 ^', NULL, NULL, NULL, '2026-02-03 11:42:25.454459');
INSERT INTO public.logs_sistema VALUES (169, 'debug', 'scheduler', 'Verificando rotinas agendadas...', NULL, NULL, NULL, '2026-02-03 11:43:25.554877');
INSERT INTO public.logs_sistema VALUES (170, 'error', 'scheduler', 'Erro na verificação: SQLSTATE[42P01]: Undefined table: 7 ERRO:  relação "rotinas" não existe
LINE 8:                     FROM rotinas r
                                 ^', NULL, NULL, NULL, '2026-02-03 11:43:25.558343');
INSERT INTO public.logs_sistema VALUES (171, 'debug', 'scheduler', 'Verificando rotinas agendadas...', NULL, NULL, NULL, '2026-02-03 11:44:25.675872');
INSERT INTO public.logs_sistema VALUES (172, 'error', 'scheduler', 'Erro na verificação: SQLSTATE[42P01]: Undefined table: 7 ERRO:  relação "rotinas" não existe
LINE 8:                     FROM rotinas r
                                 ^', NULL, NULL, NULL, '2026-02-03 11:44:25.679691');
INSERT INTO public.logs_sistema VALUES (173, 'debug', 'scheduler', 'Verificando rotinas agendadas...', NULL, NULL, NULL, '2026-02-03 11:45:25.790932');
INSERT INTO public.logs_sistema VALUES (174, 'error', 'scheduler', 'Erro na verificação: SQLSTATE[42P01]: Undefined table: 7 ERRO:  relação "rotinas" não existe
LINE 8:                     FROM rotinas r
                                 ^', NULL, NULL, NULL, '2026-02-03 11:45:25.795091');
INSERT INTO public.logs_sistema VALUES (175, 'debug', 'scheduler', 'Verificando rotinas agendadas...', NULL, NULL, NULL, '2026-02-03 11:46:25.897055');
INSERT INTO public.logs_sistema VALUES (176, 'error', 'scheduler', 'Erro na verificação: SQLSTATE[42P01]: Undefined table: 7 ERRO:  relação "rotinas" não existe
LINE 8:                     FROM rotinas r
                                 ^', NULL, NULL, NULL, '2026-02-03 11:46:25.905874');
INSERT INTO public.logs_sistema VALUES (177, 'debug', 'scheduler', 'Verificando rotinas agendadas...', NULL, NULL, NULL, '2026-02-03 11:47:26.005731');
INSERT INTO public.logs_sistema VALUES (178, 'error', 'scheduler', 'Erro na verificação: SQLSTATE[42P01]: Undefined table: 7 ERRO:  relação "rotinas" não existe
LINE 8:                     FROM rotinas r
                                 ^', NULL, NULL, NULL, '2026-02-03 11:47:26.011256');
INSERT INTO public.logs_sistema VALUES (179, 'debug', 'scheduler', 'Verificando rotinas agendadas...', NULL, NULL, NULL, '2026-02-03 11:48:26.137956');
INSERT INTO public.logs_sistema VALUES (180, 'error', 'scheduler', 'Erro na verificação: SQLSTATE[42P01]: Undefined table: 7 ERRO:  relação "rotinas" não existe
LINE 8:                     FROM rotinas r
                                 ^', NULL, NULL, NULL, '2026-02-03 11:48:26.142654');
INSERT INTO public.logs_sistema VALUES (181, 'debug', 'scheduler', 'Verificando rotinas agendadas...', NULL, NULL, NULL, '2026-02-03 11:49:26.253214');
INSERT INTO public.logs_sistema VALUES (182, 'error', 'scheduler', 'Erro na verificação: SQLSTATE[42P01]: Undefined table: 7 ERRO:  relação "rotinas" não existe
LINE 8:                     FROM rotinas r
                                 ^', NULL, NULL, NULL, '2026-02-03 11:49:26.258205');
INSERT INTO public.logs_sistema VALUES (183, 'debug', 'scheduler', 'Verificando rotinas agendadas...', NULL, NULL, NULL, '2026-02-03 11:50:26.38835');
INSERT INTO public.logs_sistema VALUES (184, 'error', 'scheduler', 'Erro na verificação: SQLSTATE[42P01]: Undefined table: 7 ERRO:  relação "rotinas" não existe
LINE 8:                     FROM rotinas r
                                 ^', NULL, NULL, NULL, '2026-02-03 11:50:26.402455');
INSERT INTO public.logs_sistema VALUES (185, 'debug', 'scheduler', 'Verificando rotinas agendadas...', NULL, NULL, NULL, '2026-02-03 11:51:26.500274');
INSERT INTO public.logs_sistema VALUES (186, 'error', 'scheduler', 'Erro na verificação: SQLSTATE[42P01]: Undefined table: 7 ERRO:  relação "rotinas" não existe
LINE 8:                     FROM rotinas r
                                 ^', NULL, NULL, NULL, '2026-02-03 11:51:26.504646');
INSERT INTO public.logs_sistema VALUES (187, 'debug', 'scheduler', 'Verificando rotinas agendadas...', NULL, NULL, NULL, '2026-02-03 11:52:26.596198');
INSERT INTO public.logs_sistema VALUES (188, 'error', 'scheduler', 'Erro na verificação: SQLSTATE[42P01]: Undefined table: 7 ERRO:  relação "rotinas" não existe
LINE 8:                     FROM rotinas r
                                 ^', NULL, NULL, NULL, '2026-02-03 11:52:26.60176');
INSERT INTO public.logs_sistema VALUES (189, 'debug', 'scheduler', 'Verificando rotinas agendadas...', NULL, NULL, NULL, '2026-02-03 11:53:26.709588');
INSERT INTO public.logs_sistema VALUES (190, 'error', 'scheduler', 'Erro na verificação: SQLSTATE[42P01]: Undefined table: 7 ERRO:  relação "rotinas" não existe
LINE 8:                     FROM rotinas r
                                 ^', NULL, NULL, NULL, '2026-02-03 11:53:26.713793');
INSERT INTO public.logs_sistema VALUES (191, 'debug', 'scheduler', 'Verificando rotinas agendadas...', NULL, NULL, NULL, '2026-02-03 11:54:26.797002');
INSERT INTO public.logs_sistema VALUES (192, 'error', 'scheduler', 'Erro na verificação: SQLSTATE[42P01]: Undefined table: 7 ERRO:  relação "rotinas" não existe
LINE 8:                     FROM rotinas r
                                 ^', NULL, NULL, NULL, '2026-02-03 11:54:26.801019');
INSERT INTO public.logs_sistema VALUES (193, 'debug', 'scheduler', 'Verificando rotinas agendadas...', NULL, NULL, NULL, '2026-02-03 11:55:26.907419');
INSERT INTO public.logs_sistema VALUES (194, 'error', 'scheduler', 'Erro na verificação: SQLSTATE[42P01]: Undefined table: 7 ERRO:  relação "rotinas" não existe
LINE 8:                     FROM rotinas r
                                 ^', NULL, NULL, NULL, '2026-02-03 11:55:26.91184');
INSERT INTO public.logs_sistema VALUES (195, 'debug', 'scheduler', 'Verificando rotinas agendadas...', NULL, NULL, NULL, '2026-02-03 11:56:27.029511');
INSERT INTO public.logs_sistema VALUES (196, 'error', 'scheduler', 'Erro na verificação: SQLSTATE[42P01]: Undefined table: 7 ERRO:  relação "rotinas" não existe
LINE 8:                     FROM rotinas r
                                 ^', NULL, NULL, NULL, '2026-02-03 11:56:27.032678');
INSERT INTO public.logs_sistema VALUES (197, 'debug', 'scheduler', 'Verificando rotinas agendadas...', NULL, NULL, NULL, '2026-02-03 11:57:27.152217');
INSERT INTO public.logs_sistema VALUES (198, 'error', 'scheduler', 'Erro na verificação: SQLSTATE[42P01]: Undefined table: 7 ERRO:  relação "rotinas" não existe
LINE 8:                     FROM rotinas r
                                 ^', NULL, NULL, NULL, '2026-02-03 11:57:27.156157');
INSERT INTO public.logs_sistema VALUES (199, 'debug', 'scheduler', 'Verificando rotinas agendadas...', NULL, NULL, NULL, '2026-02-03 11:58:27.242949');
INSERT INTO public.logs_sistema VALUES (200, 'error', 'scheduler', 'Erro na verificação: SQLSTATE[42P01]: Undefined table: 7 ERRO:  relação "rotinas" não existe
LINE 8:                     FROM rotinas r
                                 ^', NULL, NULL, NULL, '2026-02-03 11:58:27.283237');
INSERT INTO public.logs_sistema VALUES (201, 'debug', 'scheduler', 'Verificando rotinas agendadas...', NULL, NULL, NULL, '2026-02-03 11:59:27.380387');
INSERT INTO public.logs_sistema VALUES (251, 'debug', 'scheduler', 'Verificando rotinas agendadas...', NULL, NULL, NULL, '2026-02-03 12:24:30.150315');
INSERT INTO public.logs_sistema VALUES (301, 'debug', 'scheduler', 'Verificando rotinas agendadas...', NULL, NULL, NULL, '2026-02-03 12:49:32.563325');
INSERT INTO public.logs_sistema VALUES (202, 'error', 'scheduler', 'Erro na verificação: SQLSTATE[42P01]: Undefined table: 7 ERRO:  relação "rotinas" não existe
LINE 8:                     FROM rotinas r
                                 ^', NULL, NULL, NULL, '2026-02-03 11:59:27.414835');
INSERT INTO public.logs_sistema VALUES (203, 'debug', 'scheduler', 'Verificando rotinas agendadas...', NULL, NULL, NULL, '2026-02-03 12:00:27.519648');
INSERT INTO public.logs_sistema VALUES (204, 'error', 'scheduler', 'Erro na verificação: SQLSTATE[42P01]: Undefined table: 7 ERRO:  relação "rotinas" não existe
LINE 8:                     FROM rotinas r
                                 ^', NULL, NULL, NULL, '2026-02-03 12:00:27.524993');
INSERT INTO public.logs_sistema VALUES (205, 'debug', 'scheduler', 'Verificando rotinas agendadas...', NULL, NULL, NULL, '2026-02-03 12:01:27.637052');
INSERT INTO public.logs_sistema VALUES (206, 'error', 'scheduler', 'Erro na verificação: SQLSTATE[42P01]: Undefined table: 7 ERRO:  relação "rotinas" não existe
LINE 8:                     FROM rotinas r
                                 ^', NULL, NULL, NULL, '2026-02-03 12:01:27.640586');
INSERT INTO public.logs_sistema VALUES (207, 'debug', 'scheduler', 'Verificando rotinas agendadas...', NULL, NULL, NULL, '2026-02-03 12:02:27.759612');
INSERT INTO public.logs_sistema VALUES (208, 'error', 'scheduler', 'Erro na verificação: SQLSTATE[42P01]: Undefined table: 7 ERRO:  relação "rotinas" não existe
LINE 8:                     FROM rotinas r
                                 ^', NULL, NULL, NULL, '2026-02-03 12:02:27.763608');
INSERT INTO public.logs_sistema VALUES (209, 'debug', 'scheduler', 'Verificando rotinas agendadas...', NULL, NULL, NULL, '2026-02-03 12:03:27.885683');
INSERT INTO public.logs_sistema VALUES (210, 'error', 'scheduler', 'Erro na verificação: SQLSTATE[42P01]: Undefined table: 7 ERRO:  relação "rotinas" não existe
LINE 8:                     FROM rotinas r
                                 ^', NULL, NULL, NULL, '2026-02-03 12:03:27.891163');
INSERT INTO public.logs_sistema VALUES (211, 'debug', 'scheduler', 'Verificando rotinas agendadas...', NULL, NULL, NULL, '2026-02-03 12:04:28.015038');
INSERT INTO public.logs_sistema VALUES (212, 'error', 'scheduler', 'Erro na verificação: SQLSTATE[42P01]: Undefined table: 7 ERRO:  relação "rotinas" não existe
LINE 8:                     FROM rotinas r
                                 ^', NULL, NULL, NULL, '2026-02-03 12:04:28.020143');
INSERT INTO public.logs_sistema VALUES (213, 'debug', 'scheduler', 'Verificando rotinas agendadas...', NULL, NULL, NULL, '2026-02-03 12:05:28.211275');
INSERT INTO public.logs_sistema VALUES (214, 'error', 'scheduler', 'Erro na verificação: SQLSTATE[42P01]: Undefined table: 7 ERRO:  relação "rotinas" não existe
LINE 8:                     FROM rotinas r
                                 ^', NULL, NULL, NULL, '2026-02-03 12:05:28.215542');
INSERT INTO public.logs_sistema VALUES (215, 'debug', 'scheduler', 'Verificando rotinas agendadas...', NULL, NULL, NULL, '2026-02-03 12:06:28.326099');
INSERT INTO public.logs_sistema VALUES (216, 'error', 'scheduler', 'Erro na verificação: SQLSTATE[42P01]: Undefined table: 7 ERRO:  relação "rotinas" não existe
LINE 8:                     FROM rotinas r
                                 ^', NULL, NULL, NULL, '2026-02-03 12:06:28.33043');
INSERT INTO public.logs_sistema VALUES (217, 'debug', 'scheduler', 'Verificando rotinas agendadas...', NULL, NULL, NULL, '2026-02-03 12:07:28.439364');
INSERT INTO public.logs_sistema VALUES (218, 'error', 'scheduler', 'Erro na verificação: SQLSTATE[42P01]: Undefined table: 7 ERRO:  relação "rotinas" não existe
LINE 8:                     FROM rotinas r
                                 ^', NULL, NULL, NULL, '2026-02-03 12:07:28.443132');
INSERT INTO public.logs_sistema VALUES (219, 'debug', 'scheduler', 'Verificando rotinas agendadas...', NULL, NULL, NULL, '2026-02-03 12:08:28.529604');
INSERT INTO public.logs_sistema VALUES (220, 'error', 'scheduler', 'Erro na verificação: SQLSTATE[42P01]: Undefined table: 7 ERRO:  relação "rotinas" não existe
LINE 8:                     FROM rotinas r
                                 ^', NULL, NULL, NULL, '2026-02-03 12:08:28.564837');
INSERT INTO public.logs_sistema VALUES (221, 'debug', 'scheduler', 'Verificando rotinas agendadas...', NULL, NULL, NULL, '2026-02-03 12:09:28.668439');
INSERT INTO public.logs_sistema VALUES (222, 'error', 'scheduler', 'Erro na verificação: SQLSTATE[42P01]: Undefined table: 7 ERRO:  relação "rotinas" não existe
LINE 8:                     FROM rotinas r
                                 ^', NULL, NULL, NULL, '2026-02-03 12:09:28.705899');
INSERT INTO public.logs_sistema VALUES (223, 'debug', 'scheduler', 'Verificando rotinas agendadas...', NULL, NULL, NULL, '2026-02-03 12:10:28.780054');
INSERT INTO public.logs_sistema VALUES (224, 'error', 'scheduler', 'Erro na verificação: SQLSTATE[42P01]: Undefined table: 7 ERRO:  relação "rotinas" não existe
LINE 8:                     FROM rotinas r
                                 ^', NULL, NULL, NULL, '2026-02-03 12:10:28.783735');
INSERT INTO public.logs_sistema VALUES (225, 'debug', 'scheduler', 'Verificando rotinas agendadas...', NULL, NULL, NULL, '2026-02-03 12:11:28.870591');
INSERT INTO public.logs_sistema VALUES (226, 'error', 'scheduler', 'Erro na verificação: SQLSTATE[42P01]: Undefined table: 7 ERRO:  relação "rotinas" não existe
LINE 8:                     FROM rotinas r
                                 ^', NULL, NULL, NULL, '2026-02-03 12:11:28.875169');
INSERT INTO public.logs_sistema VALUES (227, 'debug', 'scheduler', 'Verificando rotinas agendadas...', NULL, NULL, NULL, '2026-02-03 12:12:28.982253');
INSERT INTO public.logs_sistema VALUES (228, 'error', 'scheduler', 'Erro na verificação: SQLSTATE[42P01]: Undefined table: 7 ERRO:  relação "rotinas" não existe
LINE 8:                     FROM rotinas r
                                 ^', NULL, NULL, NULL, '2026-02-03 12:12:29.003075');
INSERT INTO public.logs_sistema VALUES (229, 'debug', 'scheduler', 'Verificando rotinas agendadas...', NULL, NULL, NULL, '2026-02-03 12:13:29.110987');
INSERT INTO public.logs_sistema VALUES (230, 'error', 'scheduler', 'Erro na verificação: SQLSTATE[42P01]: Undefined table: 7 ERRO:  relação "rotinas" não existe
LINE 8:                     FROM rotinas r
                                 ^', NULL, NULL, NULL, '2026-02-03 12:13:29.146723');
INSERT INTO public.logs_sistema VALUES (231, 'debug', 'scheduler', 'Verificando rotinas agendadas...', NULL, NULL, NULL, '2026-02-03 12:14:29.245024');
INSERT INTO public.logs_sistema VALUES (232, 'error', 'scheduler', 'Erro na verificação: SQLSTATE[42P01]: Undefined table: 7 ERRO:  relação "rotinas" não existe
LINE 8:                     FROM rotinas r
                                 ^', NULL, NULL, NULL, '2026-02-03 12:14:29.256184');
INSERT INTO public.logs_sistema VALUES (233, 'debug', 'scheduler', 'Verificando rotinas agendadas...', NULL, NULL, NULL, '2026-02-03 12:15:29.341258');
INSERT INTO public.logs_sistema VALUES (234, 'error', 'scheduler', 'Erro na verificação: SQLSTATE[42P01]: Undefined table: 7 ERRO:  relação "rotinas" não existe
LINE 8:                     FROM rotinas r
                                 ^', NULL, NULL, NULL, '2026-02-03 12:15:29.34584');
INSERT INTO public.logs_sistema VALUES (235, 'debug', 'scheduler', 'Verificando rotinas agendadas...', NULL, NULL, NULL, '2026-02-03 12:16:29.384442');
INSERT INTO public.logs_sistema VALUES (236, 'error', 'scheduler', 'Erro na verificação: SQLSTATE[42P01]: Undefined table: 7 ERRO:  relação "rotinas" não existe
LINE 8:                     FROM rotinas r
                                 ^', NULL, NULL, NULL, '2026-02-03 12:16:29.386803');
INSERT INTO public.logs_sistema VALUES (237, 'debug', 'scheduler', 'Verificando rotinas agendadas...', NULL, NULL, NULL, '2026-02-03 12:17:29.533697');
INSERT INTO public.logs_sistema VALUES (238, 'error', 'scheduler', 'Erro na verificação: SQLSTATE[42P01]: Undefined table: 7 ERRO:  relação "rotinas" não existe
LINE 8:                     FROM rotinas r
                                 ^', NULL, NULL, NULL, '2026-02-03 12:17:29.538269');
INSERT INTO public.logs_sistema VALUES (239, 'debug', 'scheduler', 'Verificando rotinas agendadas...', NULL, NULL, NULL, '2026-02-03 12:18:29.628629');
INSERT INTO public.logs_sistema VALUES (240, 'error', 'scheduler', 'Erro na verificação: SQLSTATE[42P01]: Undefined table: 7 ERRO:  relação "rotinas" não existe
LINE 8:                     FROM rotinas r
                                 ^', NULL, NULL, NULL, '2026-02-03 12:18:29.632036');
INSERT INTO public.logs_sistema VALUES (241, 'debug', 'scheduler', 'Verificando rotinas agendadas...', NULL, NULL, NULL, '2026-02-03 12:19:29.720839');
INSERT INTO public.logs_sistema VALUES (242, 'error', 'scheduler', 'Erro na verificação: SQLSTATE[42P01]: Undefined table: 7 ERRO:  relação "rotinas" não existe
LINE 8:                     FROM rotinas r
                                 ^', NULL, NULL, NULL, '2026-02-03 12:19:29.723595');
INSERT INTO public.logs_sistema VALUES (243, 'debug', 'scheduler', 'Verificando rotinas agendadas...', NULL, NULL, NULL, '2026-02-03 12:20:29.791201');
INSERT INTO public.logs_sistema VALUES (244, 'error', 'scheduler', 'Erro na verificação: SQLSTATE[42P01]: Undefined table: 7 ERRO:  relação "rotinas" não existe
LINE 8:                     FROM rotinas r
                                 ^', NULL, NULL, NULL, '2026-02-03 12:20:29.79948');
INSERT INTO public.logs_sistema VALUES (245, 'debug', 'scheduler', 'Verificando rotinas agendadas...', NULL, NULL, NULL, '2026-02-03 12:21:29.893087');
INSERT INTO public.logs_sistema VALUES (246, 'error', 'scheduler', 'Erro na verificação: SQLSTATE[42P01]: Undefined table: 7 ERRO:  relação "rotinas" não existe
LINE 8:                     FROM rotinas r
                                 ^', NULL, NULL, NULL, '2026-02-03 12:21:29.895684');
INSERT INTO public.logs_sistema VALUES (247, 'debug', 'scheduler', 'Verificando rotinas agendadas...', NULL, NULL, NULL, '2026-02-03 12:22:29.97172');
INSERT INTO public.logs_sistema VALUES (248, 'error', 'scheduler', 'Erro na verificação: SQLSTATE[42P01]: Undefined table: 7 ERRO:  relação "rotinas" não existe
LINE 8:                     FROM rotinas r
                                 ^', NULL, NULL, NULL, '2026-02-03 12:22:29.975747');
INSERT INTO public.logs_sistema VALUES (249, 'debug', 'scheduler', 'Verificando rotinas agendadas...', NULL, NULL, NULL, '2026-02-03 12:23:30.050559');
INSERT INTO public.logs_sistema VALUES (250, 'error', 'scheduler', 'Erro na verificação: SQLSTATE[42P01]: Undefined table: 7 ERRO:  relação "rotinas" não existe
LINE 8:                     FROM rotinas r
                                 ^', NULL, NULL, NULL, '2026-02-03 12:23:30.053641');
INSERT INTO public.logs_sistema VALUES (252, 'error', 'scheduler', 'Erro na verificação: SQLSTATE[42P01]: Undefined table: 7 ERRO:  relação "rotinas" não existe
LINE 8:                     FROM rotinas r
                                 ^', NULL, NULL, NULL, '2026-02-03 12:24:30.152916');
INSERT INTO public.logs_sistema VALUES (253, 'debug', 'scheduler', 'Verificando rotinas agendadas...', NULL, NULL, NULL, '2026-02-03 12:25:30.258495');
INSERT INTO public.logs_sistema VALUES (254, 'error', 'scheduler', 'Erro na verificação: SQLSTATE[42P01]: Undefined table: 7 ERRO:  relação "rotinas" não existe
LINE 8:                     FROM rotinas r
                                 ^', NULL, NULL, NULL, '2026-02-03 12:25:30.263239');
INSERT INTO public.logs_sistema VALUES (255, 'debug', 'scheduler', 'Verificando rotinas agendadas...', NULL, NULL, NULL, '2026-02-03 12:26:30.349521');
INSERT INTO public.logs_sistema VALUES (256, 'error', 'scheduler', 'Erro na verificação: SQLSTATE[42P01]: Undefined table: 7 ERRO:  relação "rotinas" não existe
LINE 8:                     FROM rotinas r
                                 ^', NULL, NULL, NULL, '2026-02-03 12:26:30.388432');
INSERT INTO public.logs_sistema VALUES (257, 'debug', 'scheduler', 'Verificando rotinas agendadas...', NULL, NULL, NULL, '2026-02-03 12:27:30.478614');
INSERT INTO public.logs_sistema VALUES (258, 'error', 'scheduler', 'Erro na verificação: SQLSTATE[42P01]: Undefined table: 7 ERRO:  relação "rotinas" não existe
LINE 8:                     FROM rotinas r
                                 ^', NULL, NULL, NULL, '2026-02-03 12:27:30.513296');
INSERT INTO public.logs_sistema VALUES (259, 'debug', 'scheduler', 'Verificando rotinas agendadas...', NULL, NULL, NULL, '2026-02-03 12:28:30.56916');
INSERT INTO public.logs_sistema VALUES (260, 'error', 'scheduler', 'Erro na verificação: SQLSTATE[42P01]: Undefined table: 7 ERRO:  relação "rotinas" não existe
LINE 8:                     FROM rotinas r
                                 ^', NULL, NULL, NULL, '2026-02-03 12:28:30.574998');
INSERT INTO public.logs_sistema VALUES (261, 'debug', 'scheduler', 'Verificando rotinas agendadas...', NULL, NULL, NULL, '2026-02-03 12:29:30.657483');
INSERT INTO public.logs_sistema VALUES (262, 'error', 'scheduler', 'Erro na verificação: SQLSTATE[42P01]: Undefined table: 7 ERRO:  relação "rotinas" não existe
LINE 8:                     FROM rotinas r
                                 ^', NULL, NULL, NULL, '2026-02-03 12:29:30.69253');
INSERT INTO public.logs_sistema VALUES (263, 'debug', 'scheduler', 'Verificando rotinas agendadas...', NULL, NULL, NULL, '2026-02-03 12:30:30.843189');
INSERT INTO public.logs_sistema VALUES (264, 'error', 'scheduler', 'Erro na verificação: SQLSTATE[42P01]: Undefined table: 7 ERRO:  relação "rotinas" não existe
LINE 8:                     FROM rotinas r
                                 ^', NULL, NULL, NULL, '2026-02-03 12:30:30.84685');
INSERT INTO public.logs_sistema VALUES (265, 'debug', 'scheduler', 'Verificando rotinas agendadas...', NULL, NULL, NULL, '2026-02-03 12:31:30.871642');
INSERT INTO public.logs_sistema VALUES (266, 'error', 'scheduler', 'Erro na verificação: SQLSTATE[42P01]: Undefined table: 7 ERRO:  relação "rotinas" não existe
LINE 8:                     FROM rotinas r
                                 ^', NULL, NULL, NULL, '2026-02-03 12:31:30.873783');
INSERT INTO public.logs_sistema VALUES (267, 'debug', 'scheduler', 'Verificando rotinas agendadas...', NULL, NULL, NULL, '2026-02-03 12:32:31.001832');
INSERT INTO public.logs_sistema VALUES (268, 'error', 'scheduler', 'Erro na verificação: SQLSTATE[42P01]: Undefined table: 7 ERRO:  relação "rotinas" não existe
LINE 8:                     FROM rotinas r
                                 ^', NULL, NULL, NULL, '2026-02-03 12:32:31.006097');
INSERT INTO public.logs_sistema VALUES (269, 'debug', 'scheduler', 'Verificando rotinas agendadas...', NULL, NULL, NULL, '2026-02-03 12:33:31.064011');
INSERT INTO public.logs_sistema VALUES (270, 'error', 'scheduler', 'Erro na verificação: SQLSTATE[42P01]: Undefined table: 7 ERRO:  relação "rotinas" não existe
LINE 8:                     FROM rotinas r
                                 ^', NULL, NULL, NULL, '2026-02-03 12:33:31.066522');
INSERT INTO public.logs_sistema VALUES (271, 'debug', 'scheduler', 'Verificando rotinas agendadas...', NULL, NULL, NULL, '2026-02-03 12:34:31.159149');
INSERT INTO public.logs_sistema VALUES (272, 'error', 'scheduler', 'Erro na verificação: SQLSTATE[42P01]: Undefined table: 7 ERRO:  relação "rotinas" não existe
LINE 8:                     FROM rotinas r
                                 ^', NULL, NULL, NULL, '2026-02-03 12:34:31.162213');
INSERT INTO public.logs_sistema VALUES (273, 'debug', 'scheduler', 'Verificando rotinas agendadas...', NULL, NULL, NULL, '2026-02-03 12:35:31.235897');
INSERT INTO public.logs_sistema VALUES (274, 'error', 'scheduler', 'Erro na verificação: SQLSTATE[42P01]: Undefined table: 7 ERRO:  relação "rotinas" não existe
LINE 8:                     FROM rotinas r
                                 ^', NULL, NULL, NULL, '2026-02-03 12:35:31.239857');
INSERT INTO public.logs_sistema VALUES (275, 'debug', 'scheduler', 'Verificando rotinas agendadas...', NULL, NULL, NULL, '2026-02-03 12:36:31.279417');
INSERT INTO public.logs_sistema VALUES (276, 'error', 'scheduler', 'Erro na verificação: SQLSTATE[42P01]: Undefined table: 7 ERRO:  relação "rotinas" não existe
LINE 8:                     FROM rotinas r
                                 ^', NULL, NULL, NULL, '2026-02-03 12:36:31.314651');
INSERT INTO public.logs_sistema VALUES (277, 'debug', 'scheduler', 'Verificando rotinas agendadas...', NULL, NULL, NULL, '2026-02-03 12:37:31.379802');
INSERT INTO public.logs_sistema VALUES (278, 'error', 'scheduler', 'Erro na verificação: SQLSTATE[42P01]: Undefined table: 7 ERRO:  relação "rotinas" não existe
LINE 8:                     FROM rotinas r
                                 ^', NULL, NULL, NULL, '2026-02-03 12:37:31.383073');
INSERT INTO public.logs_sistema VALUES (279, 'debug', 'scheduler', 'Verificando rotinas agendadas...', NULL, NULL, NULL, '2026-02-03 12:38:31.461577');
INSERT INTO public.logs_sistema VALUES (280, 'error', 'scheduler', 'Erro na verificação: SQLSTATE[42P01]: Undefined table: 7 ERRO:  relação "rotinas" não existe
LINE 8:                     FROM rotinas r
                                 ^', NULL, NULL, NULL, '2026-02-03 12:38:31.496523');
INSERT INTO public.logs_sistema VALUES (281, 'debug', 'scheduler', 'Verificando rotinas agendadas...', NULL, NULL, NULL, '2026-02-03 12:39:31.573091');
INSERT INTO public.logs_sistema VALUES (282, 'error', 'scheduler', 'Erro na verificação: SQLSTATE[42P01]: Undefined table: 7 ERRO:  relação "rotinas" não existe
LINE 8:                     FROM rotinas r
                                 ^', NULL, NULL, NULL, '2026-02-03 12:39:31.577243');
INSERT INTO public.logs_sistema VALUES (283, 'debug', 'scheduler', 'Verificando rotinas agendadas...', NULL, NULL, NULL, '2026-02-03 12:40:31.626951');
INSERT INTO public.logs_sistema VALUES (284, 'error', 'scheduler', 'Erro na verificação: SQLSTATE[42P01]: Undefined table: 7 ERRO:  relação "rotinas" não existe
LINE 8:                     FROM rotinas r
                                 ^', NULL, NULL, NULL, '2026-02-03 12:40:31.662442');
INSERT INTO public.logs_sistema VALUES (285, 'debug', 'scheduler', 'Verificando rotinas agendadas...', NULL, NULL, NULL, '2026-02-03 12:41:31.757067');
INSERT INTO public.logs_sistema VALUES (286, 'error', 'scheduler', 'Erro na verificação: SQLSTATE[42P01]: Undefined table: 7 ERRO:  relação "rotinas" não existe
LINE 8:                     FROM rotinas r
                                 ^', NULL, NULL, NULL, '2026-02-03 12:41:31.792725');
INSERT INTO public.logs_sistema VALUES (287, 'debug', 'scheduler', 'Verificando rotinas agendadas...', NULL, NULL, NULL, '2026-02-03 12:42:31.881968');
INSERT INTO public.logs_sistema VALUES (288, 'error', 'scheduler', 'Erro na verificação: SQLSTATE[42P01]: Undefined table: 7 ERRO:  relação "rotinas" não existe
LINE 8:                     FROM rotinas r
                                 ^', NULL, NULL, NULL, '2026-02-03 12:42:31.917763');
INSERT INTO public.logs_sistema VALUES (289, 'debug', 'scheduler', 'Verificando rotinas agendadas...', NULL, NULL, NULL, '2026-02-03 12:43:32.043124');
INSERT INTO public.logs_sistema VALUES (290, 'error', 'scheduler', 'Erro na verificação: SQLSTATE[42P01]: Undefined table: 7 ERRO:  relação "rotinas" não existe
LINE 8:                     FROM rotinas r
                                 ^', NULL, NULL, NULL, '2026-02-03 12:43:32.046101');
INSERT INTO public.logs_sistema VALUES (291, 'debug', 'scheduler', 'Verificando rotinas agendadas...', NULL, NULL, NULL, '2026-02-03 12:44:32.124353');
INSERT INTO public.logs_sistema VALUES (292, 'error', 'scheduler', 'Erro na verificação: SQLSTATE[42P01]: Undefined table: 7 ERRO:  relação "rotinas" não existe
LINE 8:                     FROM rotinas r
                                 ^', NULL, NULL, NULL, '2026-02-03 12:44:32.127023');
INSERT INTO public.logs_sistema VALUES (293, 'debug', 'scheduler', 'Verificando rotinas agendadas...', NULL, NULL, NULL, '2026-02-03 12:45:32.200463');
INSERT INTO public.logs_sistema VALUES (294, 'error', 'scheduler', 'Erro na verificação: SQLSTATE[42P01]: Undefined table: 7 ERRO:  relação "rotinas" não existe
LINE 8:                     FROM rotinas r
                                 ^', NULL, NULL, NULL, '2026-02-03 12:45:32.204968');
INSERT INTO public.logs_sistema VALUES (295, 'debug', 'scheduler', 'Verificando rotinas agendadas...', NULL, NULL, NULL, '2026-02-03 12:46:32.288283');
INSERT INTO public.logs_sistema VALUES (296, 'error', 'scheduler', 'Erro na verificação: SQLSTATE[42P01]: Undefined table: 7 ERRO:  relação "rotinas" não existe
LINE 8:                     FROM rotinas r
                                 ^', NULL, NULL, NULL, '2026-02-03 12:46:32.291534');
INSERT INTO public.logs_sistema VALUES (297, 'debug', 'scheduler', 'Verificando rotinas agendadas...', NULL, NULL, NULL, '2026-02-03 12:47:32.405319');
INSERT INTO public.logs_sistema VALUES (298, 'error', 'scheduler', 'Erro na verificação: SQLSTATE[42P01]: Undefined table: 7 ERRO:  relação "rotinas" não existe
LINE 8:                     FROM rotinas r
                                 ^', NULL, NULL, NULL, '2026-02-03 12:47:32.410609');
INSERT INTO public.logs_sistema VALUES (299, 'debug', 'scheduler', 'Verificando rotinas agendadas...', NULL, NULL, NULL, '2026-02-03 12:48:32.491229');
INSERT INTO public.logs_sistema VALUES (300, 'error', 'scheduler', 'Erro na verificação: SQLSTATE[42P01]: Undefined table: 7 ERRO:  relação "rotinas" não existe
LINE 8:                     FROM rotinas r
                                 ^', NULL, NULL, NULL, '2026-02-03 12:48:32.494649');
INSERT INTO public.logs_sistema VALUES (302, 'error', 'scheduler', 'Erro na verificação: SQLSTATE[42P01]: Undefined table: 7 ERRO:  relação "rotinas" não existe
LINE 8:                     FROM rotinas r
                                 ^', NULL, NULL, NULL, '2026-02-03 12:49:32.597914');
INSERT INTO public.logs_sistema VALUES (303, 'debug', 'scheduler', 'Verificando rotinas agendadas...', NULL, NULL, NULL, '2026-02-03 12:50:32.681399');
INSERT INTO public.logs_sistema VALUES (304, 'error', 'scheduler', 'Erro na verificação: SQLSTATE[42P01]: Undefined table: 7 ERRO:  relação "rotinas" não existe
LINE 8:                     FROM rotinas r
                                 ^', NULL, NULL, NULL, '2026-02-03 12:50:32.692516');
INSERT INTO public.logs_sistema VALUES (305, 'debug', 'scheduler', 'Verificando rotinas agendadas...', NULL, NULL, NULL, '2026-02-03 12:51:32.767215');
INSERT INTO public.logs_sistema VALUES (306, 'error', 'scheduler', 'Erro na verificação: SQLSTATE[42P01]: Undefined table: 7 ERRO:  relação "rotinas" não existe
LINE 8:                     FROM rotinas r
                                 ^', NULL, NULL, NULL, '2026-02-03 12:51:32.803084');
INSERT INTO public.logs_sistema VALUES (307, 'debug', 'scheduler', 'Verificando rotinas agendadas...', NULL, NULL, NULL, '2026-02-03 12:52:32.893678');
INSERT INTO public.logs_sistema VALUES (308, 'error', 'scheduler', 'Erro na verificação: SQLSTATE[42P01]: Undefined table: 7 ERRO:  relação "rotinas" não existe
LINE 8:                     FROM rotinas r
                                 ^', NULL, NULL, NULL, '2026-02-03 12:52:32.907725');
INSERT INTO public.logs_sistema VALUES (309, 'debug', 'scheduler', 'Verificando rotinas agendadas...', NULL, NULL, NULL, '2026-02-03 12:53:32.971555');
INSERT INTO public.logs_sistema VALUES (310, 'error', 'scheduler', 'Erro na verificação: SQLSTATE[42P01]: Undefined table: 7 ERRO:  relação "rotinas" não existe
LINE 8:                     FROM rotinas r
                                 ^', NULL, NULL, NULL, '2026-02-03 12:53:32.976993');
INSERT INTO public.logs_sistema VALUES (311, 'debug', 'scheduler', 'Verificando rotinas agendadas...', NULL, NULL, NULL, '2026-02-03 12:54:33.019133');
INSERT INTO public.logs_sistema VALUES (312, 'error', 'scheduler', 'Erro na verificação: SQLSTATE[42P01]: Undefined table: 7 ERRO:  relação "rotinas" não existe
LINE 8:                     FROM rotinas r
                                 ^', NULL, NULL, NULL, '2026-02-03 12:54:33.056214');
INSERT INTO public.logs_sistema VALUES (313, 'debug', 'scheduler', 'Verificando rotinas agendadas...', NULL, NULL, NULL, '2026-02-03 12:55:33.151627');
INSERT INTO public.logs_sistema VALUES (314, 'error', 'scheduler', 'Erro na verificação: SQLSTATE[42P01]: Undefined table: 7 ERRO:  relação "rotinas" não existe
LINE 8:                     FROM rotinas r
                                 ^', NULL, NULL, NULL, '2026-02-03 12:55:33.157563');
INSERT INTO public.logs_sistema VALUES (315, 'debug', 'scheduler', 'Verificando rotinas agendadas...', NULL, NULL, NULL, '2026-02-03 12:56:33.290249');
INSERT INTO public.logs_sistema VALUES (316, 'error', 'scheduler', 'Erro na verificação: SQLSTATE[42P01]: Undefined table: 7 ERRO:  relação "rotinas" não existe
LINE 8:                     FROM rotinas r
                                 ^', NULL, NULL, NULL, '2026-02-03 12:56:33.311873');
INSERT INTO public.logs_sistema VALUES (317, 'debug', 'scheduler', 'Verificando rotinas agendadas...', NULL, NULL, NULL, '2026-02-03 12:57:33.390615');
INSERT INTO public.logs_sistema VALUES (318, 'error', 'scheduler', 'Erro na verificação: SQLSTATE[42P01]: Undefined table: 7 ERRO:  relação "rotinas" não existe
LINE 8:                     FROM rotinas r
                                 ^', NULL, NULL, NULL, '2026-02-03 12:57:33.396087');
INSERT INTO public.logs_sistema VALUES (319, 'debug', 'scheduler', 'Verificando rotinas agendadas...', NULL, NULL, NULL, '2026-02-03 12:58:33.491457');
INSERT INTO public.logs_sistema VALUES (320, 'error', 'scheduler', 'Erro na verificação: SQLSTATE[42P01]: Undefined table: 7 ERRO:  relação "rotinas" não existe
LINE 8:                     FROM rotinas r
                                 ^', NULL, NULL, NULL, '2026-02-03 12:58:33.52688');
INSERT INTO public.logs_sistema VALUES (321, 'debug', 'scheduler', 'Verificando rotinas agendadas...', NULL, NULL, NULL, '2026-02-03 12:59:33.617756');
INSERT INTO public.logs_sistema VALUES (322, 'error', 'scheduler', 'Erro na verificação: SQLSTATE[42P01]: Undefined table: 7 ERRO:  relação "rotinas" não existe
LINE 8:                     FROM rotinas r
                                 ^', NULL, NULL, NULL, '2026-02-03 12:59:33.65287');
INSERT INTO public.logs_sistema VALUES (323, 'debug', 'scheduler', 'Verificando rotinas agendadas...', NULL, NULL, NULL, '2026-02-03 13:00:33.776459');
INSERT INTO public.logs_sistema VALUES (324, 'error', 'scheduler', 'Erro na verificação: SQLSTATE[42P01]: Undefined table: 7 ERRO:  relação "rotinas" não existe
LINE 8:                     FROM rotinas r
                                 ^', NULL, NULL, NULL, '2026-02-03 13:00:33.784072');
INSERT INTO public.logs_sistema VALUES (325, 'debug', 'scheduler', 'Verificando rotinas agendadas...', NULL, NULL, NULL, '2026-02-03 13:01:33.903206');
INSERT INTO public.logs_sistema VALUES (326, 'error', 'scheduler', 'Erro na verificação: SQLSTATE[42P01]: Undefined table: 7 ERRO:  relação "rotinas" não existe
LINE 8:                     FROM rotinas r
                                 ^', NULL, NULL, NULL, '2026-02-03 13:01:33.944052');
INSERT INTO public.logs_sistema VALUES (327, 'debug', 'scheduler', 'Verificando rotinas agendadas...', NULL, NULL, NULL, '2026-02-03 13:02:34.036147');
INSERT INTO public.logs_sistema VALUES (328, 'error', 'scheduler', 'Erro na verificação: SQLSTATE[42P01]: Undefined table: 7 ERRO:  relação "rotinas" não existe
LINE 8:                     FROM rotinas r
                                 ^', NULL, NULL, NULL, '2026-02-03 13:02:34.039452');
INSERT INTO public.logs_sistema VALUES (329, 'debug', 'scheduler', 'Verificando rotinas agendadas...', NULL, NULL, NULL, '2026-02-03 13:03:34.156663');
INSERT INTO public.logs_sistema VALUES (330, 'error', 'scheduler', 'Erro na verificação: SQLSTATE[42P01]: Undefined table: 7 ERRO:  relação "rotinas" não existe
LINE 8:                     FROM rotinas r
                                 ^', NULL, NULL, NULL, '2026-02-03 13:03:34.161085');
INSERT INTO public.logs_sistema VALUES (331, 'debug', 'scheduler', 'Verificando rotinas agendadas...', NULL, NULL, NULL, '2026-02-03 13:04:34.294297');
INSERT INTO public.logs_sistema VALUES (332, 'error', 'scheduler', 'Erro na verificação: SQLSTATE[42P01]: Undefined table: 7 ERRO:  relação "rotinas" não existe
LINE 8:                     FROM rotinas r
                                 ^', NULL, NULL, NULL, '2026-02-03 13:04:34.307272');
INSERT INTO public.logs_sistema VALUES (333, 'debug', 'scheduler', 'Verificando rotinas agendadas...', NULL, NULL, NULL, '2026-02-03 13:05:34.401188');
INSERT INTO public.logs_sistema VALUES (334, 'error', 'scheduler', 'Erro na verificação: SQLSTATE[42P01]: Undefined table: 7 ERRO:  relação "rotinas" não existe
LINE 8:                     FROM rotinas r
                                 ^', NULL, NULL, NULL, '2026-02-03 13:05:34.441893');
INSERT INTO public.logs_sistema VALUES (335, 'debug', 'scheduler', 'Verificando rotinas agendadas...', NULL, NULL, NULL, '2026-02-03 13:06:34.568633');
INSERT INTO public.logs_sistema VALUES (336, 'error', 'scheduler', 'Erro na verificação: SQLSTATE[42P01]: Undefined table: 7 ERRO:  relação "rotinas" não existe
LINE 8:                     FROM rotinas r
                                 ^', NULL, NULL, NULL, '2026-02-03 13:06:34.573745');
INSERT INTO public.logs_sistema VALUES (337, 'debug', 'scheduler', 'Verificando rotinas agendadas...', NULL, NULL, NULL, '2026-02-03 13:07:34.670738');
INSERT INTO public.logs_sistema VALUES (338, 'error', 'scheduler', 'Erro na verificação: SQLSTATE[42P01]: Undefined table: 7 ERRO:  relação "rotinas" não existe
LINE 8:                     FROM rotinas r
                                 ^', NULL, NULL, NULL, '2026-02-03 13:07:34.676246');
INSERT INTO public.logs_sistema VALUES (339, 'debug', 'scheduler', 'Verificando rotinas agendadas...', NULL, NULL, NULL, '2026-02-03 13:08:34.788596');
INSERT INTO public.logs_sistema VALUES (340, 'error', 'scheduler', 'Erro na verificação: SQLSTATE[42P01]: Undefined table: 7 ERRO:  relação "rotinas" não existe
LINE 8:                     FROM rotinas r
                                 ^', NULL, NULL, NULL, '2026-02-03 13:08:34.792025');
INSERT INTO public.logs_sistema VALUES (341, 'debug', 'scheduler', 'Verificando rotinas agendadas...', NULL, NULL, NULL, '2026-02-03 13:09:34.88845');
INSERT INTO public.logs_sistema VALUES (342, 'error', 'scheduler', 'Erro na verificação: SQLSTATE[42P01]: Undefined table: 7 ERRO:  relação "rotinas" não existe
LINE 8:                     FROM rotinas r
                                 ^', NULL, NULL, NULL, '2026-02-03 13:09:34.893428');
INSERT INTO public.logs_sistema VALUES (343, 'debug', 'scheduler', 'Verificando rotinas agendadas...', NULL, NULL, NULL, '2026-02-03 13:10:34.995694');
INSERT INTO public.logs_sistema VALUES (344, 'error', 'scheduler', 'Erro na verificação: SQLSTATE[42P01]: Undefined table: 7 ERRO:  relação "rotinas" não existe
LINE 8:                     FROM rotinas r
                                 ^', NULL, NULL, NULL, '2026-02-03 13:10:35.000347');
INSERT INTO public.logs_sistema VALUES (345, 'debug', 'scheduler', 'Verificando rotinas agendadas...', NULL, NULL, NULL, '2026-02-03 13:11:35.081846');
INSERT INTO public.logs_sistema VALUES (346, 'error', 'scheduler', 'Erro na verificação: SQLSTATE[42P01]: Undefined table: 7 ERRO:  relação "rotinas" não existe
LINE 8:                     FROM rotinas r
                                 ^', NULL, NULL, NULL, '2026-02-03 13:11:35.084589');
INSERT INTO public.logs_sistema VALUES (347, 'debug', 'scheduler', 'Verificando rotinas agendadas...', NULL, NULL, NULL, '2026-02-03 13:12:35.15052');
INSERT INTO public.logs_sistema VALUES (348, 'error', 'scheduler', 'Erro na verificação: SQLSTATE[42P01]: Undefined table: 7 ERRO:  relação "rotinas" não existe
LINE 8:                     FROM rotinas r
                                 ^', NULL, NULL, NULL, '2026-02-03 13:12:35.154063');
INSERT INTO public.logs_sistema VALUES (349, 'debug', 'scheduler', 'Verificando rotinas agendadas...', NULL, NULL, NULL, '2026-02-03 13:13:35.242654');
INSERT INTO public.logs_sistema VALUES (350, 'error', 'scheduler', 'Erro na verificação: SQLSTATE[42P01]: Undefined table: 7 ERRO:  relação "rotinas" não existe
LINE 8:                     FROM rotinas r
                                 ^', NULL, NULL, NULL, '2026-02-03 13:13:35.246789');
INSERT INTO public.logs_sistema VALUES (351, 'debug', 'scheduler', 'Verificando rotinas agendadas...', NULL, NULL, NULL, '2026-02-03 13:14:35.336886');
INSERT INTO public.logs_sistema VALUES (352, 'error', 'scheduler', 'Erro na verificação: SQLSTATE[42P01]: Undefined table: 7 ERRO:  relação "rotinas" não existe
LINE 8:                     FROM rotinas r
                                 ^', NULL, NULL, NULL, '2026-02-03 13:14:35.355512');
INSERT INTO public.logs_sistema VALUES (353, 'debug', 'scheduler', 'Verificando rotinas agendadas...', NULL, NULL, NULL, '2026-02-03 13:15:35.497737');
INSERT INTO public.logs_sistema VALUES (354, 'error', 'scheduler', 'Erro na verificação: SQLSTATE[42P01]: Undefined table: 7 ERRO:  relação "rotinas" não existe
LINE 8:                     FROM rotinas r
                                 ^', NULL, NULL, NULL, '2026-02-03 13:15:35.539703');
INSERT INTO public.logs_sistema VALUES (355, 'debug', 'scheduler', 'Verificando rotinas agendadas...', NULL, NULL, NULL, '2026-02-03 13:16:35.598764');
INSERT INTO public.logs_sistema VALUES (356, 'error', 'scheduler', 'Erro na verificação: SQLSTATE[42P01]: Undefined table: 7 ERRO:  relação "rotinas" não existe
LINE 8:                     FROM rotinas r
                                 ^', NULL, NULL, NULL, '2026-02-03 13:16:35.601465');
INSERT INTO public.logs_sistema VALUES (357, 'debug', 'scheduler', 'Verificando rotinas agendadas...', NULL, NULL, NULL, '2026-02-03 13:17:35.69919');
INSERT INTO public.logs_sistema VALUES (358, 'error', 'scheduler', 'Erro na verificação: SQLSTATE[42P01]: Undefined table: 7 ERRO:  relação "rotinas" não existe
LINE 8:                     FROM rotinas r
                                 ^', NULL, NULL, NULL, '2026-02-03 13:17:35.731706');
INSERT INTO public.logs_sistema VALUES (359, 'debug', 'scheduler', 'Verificando rotinas agendadas...', NULL, NULL, NULL, '2026-02-03 13:18:35.816948');
INSERT INTO public.logs_sistema VALUES (360, 'error', 'scheduler', 'Erro na verificação: SQLSTATE[42P01]: Undefined table: 7 ERRO:  relação "rotinas" não existe
LINE 8:                     FROM rotinas r
                                 ^', NULL, NULL, NULL, '2026-02-03 13:18:35.824583');
INSERT INTO public.logs_sistema VALUES (361, 'debug', 'scheduler', 'Verificando rotinas agendadas...', NULL, NULL, NULL, '2026-02-03 13:19:35.955349');
INSERT INTO public.logs_sistema VALUES (362, 'error', 'scheduler', 'Erro na verificação: SQLSTATE[42P01]: Undefined table: 7 ERRO:  relação "rotinas" não existe
LINE 8:                     FROM rotinas r
                                 ^', NULL, NULL, NULL, '2026-02-03 13:19:35.992507');
INSERT INTO public.logs_sistema VALUES (363, 'debug', 'scheduler', 'Verificando rotinas agendadas...', NULL, NULL, NULL, '2026-02-03 13:20:36.042882');
INSERT INTO public.logs_sistema VALUES (364, 'error', 'scheduler', 'Erro na verificação: SQLSTATE[42P01]: Undefined table: 7 ERRO:  relação "rotinas" não existe
LINE 8:                     FROM rotinas r
                                 ^', NULL, NULL, NULL, '2026-02-03 13:20:36.049317');
INSERT INTO public.logs_sistema VALUES (365, 'debug', 'scheduler', 'Verificando rotinas agendadas...', NULL, NULL, NULL, '2026-02-03 13:21:36.115816');
INSERT INTO public.logs_sistema VALUES (366, 'error', 'scheduler', 'Erro na verificação: SQLSTATE[42P01]: Undefined table: 7 ERRO:  relação "rotinas" não existe
LINE 8:                     FROM rotinas r
                                 ^', NULL, NULL, NULL, '2026-02-03 13:21:36.118547');
INSERT INTO public.logs_sistema VALUES (367, 'debug', 'scheduler', 'Verificando rotinas agendadas...', NULL, NULL, NULL, '2026-02-03 13:22:36.225209');
INSERT INTO public.logs_sistema VALUES (368, 'error', 'scheduler', 'Erro na verificação: SQLSTATE[42P01]: Undefined table: 7 ERRO:  relação "rotinas" não existe
LINE 8:                     FROM rotinas r
                                 ^', NULL, NULL, NULL, '2026-02-03 13:22:36.229573');
INSERT INTO public.logs_sistema VALUES (369, 'debug', 'scheduler', 'Verificando rotinas agendadas...', NULL, NULL, NULL, '2026-02-03 13:23:36.33669');
INSERT INTO public.logs_sistema VALUES (370, 'error', 'scheduler', 'Erro na verificação: SQLSTATE[42P01]: Undefined table: 7 ERRO:  relação "rotinas" não existe
LINE 8:                     FROM rotinas r
                                 ^', NULL, NULL, NULL, '2026-02-03 13:23:36.342629');
INSERT INTO public.logs_sistema VALUES (371, 'debug', 'scheduler', 'Verificando rotinas agendadas...', NULL, NULL, NULL, '2026-02-03 13:24:36.470802');
INSERT INTO public.logs_sistema VALUES (372, 'error', 'scheduler', 'Erro na verificação: SQLSTATE[42P01]: Undefined table: 7 ERRO:  relação "rotinas" não existe
LINE 8:                     FROM rotinas r
                                 ^', NULL, NULL, NULL, '2026-02-03 13:24:36.475034');
INSERT INTO public.logs_sistema VALUES (373, 'debug', 'scheduler', 'Verificando rotinas agendadas...', NULL, NULL, NULL, '2026-02-03 13:25:36.57776');
INSERT INTO public.logs_sistema VALUES (374, 'error', 'scheduler', 'Erro na verificação: SQLSTATE[42P01]: Undefined table: 7 ERRO:  relação "rotinas" não existe
LINE 8:                     FROM rotinas r
                                 ^', NULL, NULL, NULL, '2026-02-03 13:25:36.617623');
INSERT INTO public.logs_sistema VALUES (375, 'debug', 'scheduler', 'Verificando rotinas agendadas...', NULL, NULL, NULL, '2026-02-03 13:26:36.711794');
INSERT INTO public.logs_sistema VALUES (376, 'error', 'scheduler', 'Erro na verificação: SQLSTATE[42P01]: Undefined table: 7 ERRO:  relação "rotinas" não existe
LINE 8:                     FROM rotinas r
                                 ^', NULL, NULL, NULL, '2026-02-03 13:26:36.723909');
INSERT INTO public.logs_sistema VALUES (377, 'debug', 'scheduler', 'Verificando rotinas agendadas...', NULL, NULL, NULL, '2026-02-03 13:27:36.776953');
INSERT INTO public.logs_sistema VALUES (378, 'error', 'scheduler', 'Erro na verificação: SQLSTATE[42P01]: Undefined table: 7 ERRO:  relação "rotinas" não existe
LINE 8:                     FROM rotinas r
                                 ^', NULL, NULL, NULL, '2026-02-03 13:27:36.796948');
INSERT INTO public.logs_sistema VALUES (379, 'debug', 'scheduler', 'Verificando rotinas agendadas...', NULL, NULL, NULL, '2026-02-03 13:28:36.916361');
INSERT INTO public.logs_sistema VALUES (380, 'error', 'scheduler', 'Erro na verificação: SQLSTATE[42P01]: Undefined table: 7 ERRO:  relação "rotinas" não existe
LINE 8:                     FROM rotinas r
                                 ^', NULL, NULL, NULL, '2026-02-03 13:28:36.922579');
INSERT INTO public.logs_sistema VALUES (381, 'debug', 'scheduler', 'Verificando rotinas agendadas...', NULL, NULL, NULL, '2026-02-03 13:29:37.01333');
INSERT INTO public.logs_sistema VALUES (382, 'error', 'scheduler', 'Erro na verificação: SQLSTATE[42P01]: Undefined table: 7 ERRO:  relação "rotinas" não existe
LINE 8:                     FROM rotinas r
                                 ^', NULL, NULL, NULL, '2026-02-03 13:29:37.022785');
INSERT INTO public.logs_sistema VALUES (383, 'debug', 'scheduler', 'Verificando rotinas agendadas...', NULL, NULL, NULL, '2026-02-03 13:30:37.124111');
INSERT INTO public.logs_sistema VALUES (384, 'error', 'scheduler', 'Erro na verificação: SQLSTATE[42P01]: Undefined table: 7 ERRO:  relação "rotinas" não existe
LINE 8:                     FROM rotinas r
                                 ^', NULL, NULL, NULL, '2026-02-03 13:30:37.159785');
INSERT INTO public.logs_sistema VALUES (385, 'debug', 'scheduler', 'Verificando rotinas agendadas...', NULL, NULL, NULL, '2026-02-03 13:31:37.264979');
INSERT INTO public.logs_sistema VALUES (386, 'error', 'scheduler', 'Erro na verificação: SQLSTATE[42P01]: Undefined table: 7 ERRO:  relação "rotinas" não existe
LINE 8:                     FROM rotinas r
                                 ^', NULL, NULL, NULL, '2026-02-03 13:31:37.300365');
INSERT INTO public.logs_sistema VALUES (387, 'debug', 'scheduler', 'Verificando rotinas agendadas...', NULL, NULL, NULL, '2026-02-03 13:32:37.367798');
INSERT INTO public.logs_sistema VALUES (388, 'error', 'scheduler', 'Erro na verificação: SQLSTATE[42P01]: Undefined table: 7 ERRO:  relação "rotinas" não existe
LINE 8:                     FROM rotinas r
                                 ^', NULL, NULL, NULL, '2026-02-03 13:32:37.372328');
INSERT INTO public.logs_sistema VALUES (389, 'debug', 'scheduler', 'Verificando rotinas agendadas...', NULL, NULL, NULL, '2026-02-03 13:33:37.479123');
INSERT INTO public.logs_sistema VALUES (390, 'error', 'scheduler', 'Erro na verificação: SQLSTATE[42P01]: Undefined table: 7 ERRO:  relação "rotinas" não existe
LINE 8:                     FROM rotinas r
                                 ^', NULL, NULL, NULL, '2026-02-03 13:33:37.482125');
INSERT INTO public.logs_sistema VALUES (391, 'debug', 'scheduler', 'Verificando rotinas agendadas...', NULL, NULL, NULL, '2026-02-03 13:34:37.60529');
INSERT INTO public.logs_sistema VALUES (392, 'error', 'scheduler', 'Erro na verificação: SQLSTATE[42P01]: Undefined table: 7 ERRO:  relação "rotinas" não existe
LINE 8:                     FROM rotinas r
                                 ^', NULL, NULL, NULL, '2026-02-03 13:34:37.640273');
INSERT INTO public.logs_sistema VALUES (393, 'debug', 'scheduler', 'Verificando rotinas agendadas...', NULL, NULL, NULL, '2026-02-03 13:35:37.739768');
INSERT INTO public.logs_sistema VALUES (394, 'error', 'scheduler', 'Erro na verificação: SQLSTATE[42P01]: Undefined table: 7 ERRO:  relação "rotinas" não existe
LINE 8:                     FROM rotinas r
                                 ^', NULL, NULL, NULL, '2026-02-03 13:35:37.746046');
INSERT INTO public.logs_sistema VALUES (395, 'debug', 'scheduler', 'Verificando rotinas agendadas...', NULL, NULL, NULL, '2026-02-03 13:36:37.850335');
INSERT INTO public.logs_sistema VALUES (396, 'error', 'scheduler', 'Erro na verificação: SQLSTATE[42P01]: Undefined table: 7 ERRO:  relação "rotinas" não existe
LINE 8:                     FROM rotinas r
                                 ^', NULL, NULL, NULL, '2026-02-03 13:36:37.854207');
INSERT INTO public.logs_sistema VALUES (397, 'debug', 'scheduler', 'Verificando rotinas agendadas...', NULL, NULL, NULL, '2026-02-03 13:37:37.927745');
INSERT INTO public.logs_sistema VALUES (398, 'error', 'scheduler', 'Erro na verificação: SQLSTATE[42P01]: Undefined table: 7 ERRO:  relação "rotinas" não existe
LINE 8:                     FROM rotinas r
                                 ^', NULL, NULL, NULL, '2026-02-03 13:37:37.93024');
INSERT INTO public.logs_sistema VALUES (399, 'debug', 'scheduler', 'Verificando rotinas agendadas...', NULL, NULL, NULL, '2026-02-03 13:38:38.004566');
INSERT INTO public.logs_sistema VALUES (449, 'debug', 'scheduler', 'Verificando rotinas agendadas...', NULL, NULL, NULL, '2026-02-03 14:03:40.741836');
INSERT INTO public.logs_sistema VALUES (499, 'debug', 'scheduler', 'Verificando rotinas agendadas...', NULL, NULL, NULL, '2026-02-03 14:28:43.634519');
INSERT INTO public.logs_sistema VALUES (400, 'error', 'scheduler', 'Erro na verificação: SQLSTATE[42P01]: Undefined table: 7 ERRO:  relação "rotinas" não existe
LINE 8:                     FROM rotinas r
                                 ^', NULL, NULL, NULL, '2026-02-03 13:38:38.008951');
INSERT INTO public.logs_sistema VALUES (401, 'debug', 'scheduler', 'Verificando rotinas agendadas...', NULL, NULL, NULL, '2026-02-03 13:39:38.08514');
INSERT INTO public.logs_sistema VALUES (402, 'error', 'scheduler', 'Erro na verificação: SQLSTATE[42P01]: Undefined table: 7 ERRO:  relação "rotinas" não existe
LINE 8:                     FROM rotinas r
                                 ^', NULL, NULL, NULL, '2026-02-03 13:39:38.087963');
INSERT INTO public.logs_sistema VALUES (403, 'debug', 'scheduler', 'Verificando rotinas agendadas...', NULL, NULL, NULL, '2026-02-03 13:40:38.187828');
INSERT INTO public.logs_sistema VALUES (404, 'error', 'scheduler', 'Erro na verificação: SQLSTATE[42P01]: Undefined table: 7 ERRO:  relação "rotinas" não existe
LINE 8:                     FROM rotinas r
                                 ^', NULL, NULL, NULL, '2026-02-03 13:40:38.191644');
INSERT INTO public.logs_sistema VALUES (405, 'debug', 'scheduler', 'Verificando rotinas agendadas...', NULL, NULL, NULL, '2026-02-03 13:41:38.302721');
INSERT INTO public.logs_sistema VALUES (406, 'error', 'scheduler', 'Erro na verificação: SQLSTATE[42P01]: Undefined table: 7 ERRO:  relação "rotinas" não existe
LINE 8:                     FROM rotinas r
                                 ^', NULL, NULL, NULL, '2026-02-03 13:41:38.30499');
INSERT INTO public.logs_sistema VALUES (407, 'debug', 'scheduler', 'Verificando rotinas agendadas...', NULL, NULL, NULL, '2026-02-03 13:42:38.417646');
INSERT INTO public.logs_sistema VALUES (408, 'error', 'scheduler', 'Erro na verificação: SQLSTATE[42P01]: Undefined table: 7 ERRO:  relação "rotinas" não existe
LINE 8:                     FROM rotinas r
                                 ^', NULL, NULL, NULL, '2026-02-03 13:42:38.422613');
INSERT INTO public.logs_sistema VALUES (409, 'debug', 'scheduler', 'Verificando rotinas agendadas...', NULL, NULL, NULL, '2026-02-03 13:43:38.52404');
INSERT INTO public.logs_sistema VALUES (410, 'error', 'scheduler', 'Erro na verificação: SQLSTATE[42P01]: Undefined table: 7 ERRO:  relação "rotinas" não existe
LINE 8:                     FROM rotinas r
                                 ^', NULL, NULL, NULL, '2026-02-03 13:43:38.527851');
INSERT INTO public.logs_sistema VALUES (411, 'debug', 'scheduler', 'Verificando rotinas agendadas...', NULL, NULL, NULL, '2026-02-03 13:44:38.591713');
INSERT INTO public.logs_sistema VALUES (412, 'error', 'scheduler', 'Erro na verificação: SQLSTATE[42P01]: Undefined table: 7 ERRO:  relação "rotinas" não existe
LINE 8:                     FROM rotinas r
                                 ^', NULL, NULL, NULL, '2026-02-03 13:44:38.59642');
INSERT INTO public.logs_sistema VALUES (413, 'debug', 'scheduler', 'Verificando rotinas agendadas...', NULL, NULL, NULL, '2026-02-03 13:45:38.683389');
INSERT INTO public.logs_sistema VALUES (414, 'error', 'scheduler', 'Erro na verificação: SQLSTATE[42P01]: Undefined table: 7 ERRO:  relação "rotinas" não existe
LINE 8:                     FROM rotinas r
                                 ^', NULL, NULL, NULL, '2026-02-03 13:45:38.687376');
INSERT INTO public.logs_sistema VALUES (415, 'debug', 'scheduler', 'Verificando rotinas agendadas...', NULL, NULL, NULL, '2026-02-03 13:46:38.765208');
INSERT INTO public.logs_sistema VALUES (416, 'error', 'scheduler', 'Erro na verificação: SQLSTATE[42P01]: Undefined table: 7 ERRO:  relação "rotinas" não existe
LINE 8:                     FROM rotinas r
                                 ^', NULL, NULL, NULL, '2026-02-03 13:46:38.768278');
INSERT INTO public.logs_sistema VALUES (417, 'debug', 'scheduler', 'Verificando rotinas agendadas...', NULL, NULL, NULL, '2026-02-03 13:47:38.86779');
INSERT INTO public.logs_sistema VALUES (418, 'error', 'scheduler', 'Erro na verificação: SQLSTATE[42P01]: Undefined table: 7 ERRO:  relação "rotinas" não existe
LINE 8:                     FROM rotinas r
                                 ^', NULL, NULL, NULL, '2026-02-03 13:47:38.886278');
INSERT INTO public.logs_sistema VALUES (419, 'debug', 'scheduler', 'Verificando rotinas agendadas...', NULL, NULL, NULL, '2026-02-03 13:48:38.981623');
INSERT INTO public.logs_sistema VALUES (420, 'error', 'scheduler', 'Erro na verificação: SQLSTATE[42P01]: Undefined table: 7 ERRO:  relação "rotinas" não existe
LINE 8:                     FROM rotinas r
                                 ^', NULL, NULL, NULL, '2026-02-03 13:48:38.987797');
INSERT INTO public.logs_sistema VALUES (421, 'debug', 'scheduler', 'Verificando rotinas agendadas...', NULL, NULL, NULL, '2026-02-03 13:49:39.0806');
INSERT INTO public.logs_sistema VALUES (422, 'error', 'scheduler', 'Erro na verificação: SQLSTATE[42P01]: Undefined table: 7 ERRO:  relação "rotinas" não existe
LINE 8:                     FROM rotinas r
                                 ^', NULL, NULL, NULL, '2026-02-03 13:49:39.082978');
INSERT INTO public.logs_sistema VALUES (423, 'debug', 'scheduler', 'Verificando rotinas agendadas...', NULL, NULL, NULL, '2026-02-03 13:50:39.202433');
INSERT INTO public.logs_sistema VALUES (424, 'error', 'scheduler', 'Erro na verificação: SQLSTATE[42P01]: Undefined table: 7 ERRO:  relação "rotinas" não existe
LINE 8:                     FROM rotinas r
                                 ^', NULL, NULL, NULL, '2026-02-03 13:50:39.218252');
INSERT INTO public.logs_sistema VALUES (425, 'debug', 'scheduler', 'Verificando rotinas agendadas...', NULL, NULL, NULL, '2026-02-03 13:51:39.301929');
INSERT INTO public.logs_sistema VALUES (426, 'error', 'scheduler', 'Erro na verificação: SQLSTATE[42P01]: Undefined table: 7 ERRO:  relação "rotinas" não existe
LINE 8:                     FROM rotinas r
                                 ^', NULL, NULL, NULL, '2026-02-03 13:51:39.329414');
INSERT INTO public.logs_sistema VALUES (427, 'debug', 'scheduler', 'Verificando rotinas agendadas...', NULL, NULL, NULL, '2026-02-03 13:52:39.497985');
INSERT INTO public.logs_sistema VALUES (428, 'error', 'scheduler', 'Erro na verificação: SQLSTATE[42P01]: Undefined table: 7 ERRO:  relação "rotinas" não existe
LINE 8:                     FROM rotinas r
                                 ^', NULL, NULL, NULL, '2026-02-03 13:52:39.501763');
INSERT INTO public.logs_sistema VALUES (429, 'debug', 'scheduler', 'Verificando rotinas agendadas...', NULL, NULL, NULL, '2026-02-03 13:53:39.605979');
INSERT INTO public.logs_sistema VALUES (430, 'error', 'scheduler', 'Erro na verificação: SQLSTATE[42P01]: Undefined table: 7 ERRO:  relação "rotinas" não existe
LINE 8:                     FROM rotinas r
                                 ^', NULL, NULL, NULL, '2026-02-03 13:53:39.610263');
INSERT INTO public.logs_sistema VALUES (431, 'debug', 'scheduler', 'Verificando rotinas agendadas...', NULL, NULL, NULL, '2026-02-03 13:54:39.651752');
INSERT INTO public.logs_sistema VALUES (432, 'error', 'scheduler', 'Erro na verificação: SQLSTATE[42P01]: Undefined table: 7 ERRO:  relação "rotinas" não existe
LINE 8:                     FROM rotinas r
                                 ^', NULL, NULL, NULL, '2026-02-03 13:54:39.654538');
INSERT INTO public.logs_sistema VALUES (433, 'debug', 'scheduler', 'Verificando rotinas agendadas...', NULL, NULL, NULL, '2026-02-03 13:55:39.740854');
INSERT INTO public.logs_sistema VALUES (434, 'error', 'scheduler', 'Erro na verificação: SQLSTATE[42P01]: Undefined table: 7 ERRO:  relação "rotinas" não existe
LINE 8:                     FROM rotinas r
                                 ^', NULL, NULL, NULL, '2026-02-03 13:55:39.746063');
INSERT INTO public.logs_sistema VALUES (435, 'debug', 'scheduler', 'Verificando rotinas agendadas...', NULL, NULL, NULL, '2026-02-03 13:56:39.824742');
INSERT INTO public.logs_sistema VALUES (436, 'error', 'scheduler', 'Erro na verificação: SQLSTATE[42P01]: Undefined table: 7 ERRO:  relação "rotinas" não existe
LINE 8:                     FROM rotinas r
                                 ^', NULL, NULL, NULL, '2026-02-03 13:56:39.828102');
INSERT INTO public.logs_sistema VALUES (437, 'debug', 'scheduler', 'Verificando rotinas agendadas...', NULL, NULL, NULL, '2026-02-03 13:57:39.955611');
INSERT INTO public.logs_sistema VALUES (438, 'error', 'scheduler', 'Erro na verificação: SQLSTATE[42P01]: Undefined table: 7 ERRO:  relação "rotinas" não existe
LINE 8:                     FROM rotinas r
                                 ^', NULL, NULL, NULL, '2026-02-03 13:57:39.990486');
INSERT INTO public.logs_sistema VALUES (439, 'debug', 'scheduler', 'Verificando rotinas agendadas...', NULL, NULL, NULL, '2026-02-03 13:58:40.108903');
INSERT INTO public.logs_sistema VALUES (440, 'error', 'scheduler', 'Erro na verificação: SQLSTATE[42P01]: Undefined table: 7 ERRO:  relação "rotinas" não existe
LINE 8:                     FROM rotinas r
                                 ^', NULL, NULL, NULL, '2026-02-03 13:58:40.112832');
INSERT INTO public.logs_sistema VALUES (441, 'debug', 'scheduler', 'Verificando rotinas agendadas...', NULL, NULL, NULL, '2026-02-03 13:59:40.235285');
INSERT INTO public.logs_sistema VALUES (442, 'error', 'scheduler', 'Erro na verificação: SQLSTATE[42P01]: Undefined table: 7 ERRO:  relação "rotinas" não existe
LINE 8:                     FROM rotinas r
                                 ^', NULL, NULL, NULL, '2026-02-03 13:59:40.241138');
INSERT INTO public.logs_sistema VALUES (443, 'debug', 'scheduler', 'Verificando rotinas agendadas...', NULL, NULL, NULL, '2026-02-03 14:00:40.395526');
INSERT INTO public.logs_sistema VALUES (444, 'error', 'scheduler', 'Erro na verificação: SQLSTATE[42P01]: Undefined table: 7 ERRO:  relação "rotinas" não existe
LINE 8:                     FROM rotinas r
                                 ^', NULL, NULL, NULL, '2026-02-03 14:00:40.403498');
INSERT INTO public.logs_sistema VALUES (445, 'debug', 'scheduler', 'Verificando rotinas agendadas...', NULL, NULL, NULL, '2026-02-03 14:01:40.521233');
INSERT INTO public.logs_sistema VALUES (446, 'error', 'scheduler', 'Erro na verificação: SQLSTATE[42P01]: Undefined table: 7 ERRO:  relação "rotinas" não existe
LINE 8:                     FROM rotinas r
                                 ^', NULL, NULL, NULL, '2026-02-03 14:01:40.555865');
INSERT INTO public.logs_sistema VALUES (447, 'debug', 'scheduler', 'Verificando rotinas agendadas...', NULL, NULL, NULL, '2026-02-03 14:02:40.637433');
INSERT INTO public.logs_sistema VALUES (448, 'error', 'scheduler', 'Erro na verificação: SQLSTATE[42P01]: Undefined table: 7 ERRO:  relação "rotinas" não existe
LINE 8:                     FROM rotinas r
                                 ^', NULL, NULL, NULL, '2026-02-03 14:02:40.645434');
INSERT INTO public.logs_sistema VALUES (450, 'error', 'scheduler', 'Erro na verificação: SQLSTATE[42P01]: Undefined table: 7 ERRO:  relação "rotinas" não existe
LINE 8:                     FROM rotinas r
                                 ^', NULL, NULL, NULL, '2026-02-03 14:03:40.747437');
INSERT INTO public.logs_sistema VALUES (451, 'debug', 'scheduler', 'Verificando rotinas agendadas...', NULL, NULL, NULL, '2026-02-03 14:04:40.84984');
INSERT INTO public.logs_sistema VALUES (452, 'error', 'scheduler', 'Erro na verificação: SQLSTATE[42P01]: Undefined table: 7 ERRO:  relação "rotinas" não existe
LINE 8:                     FROM rotinas r
                                 ^', NULL, NULL, NULL, '2026-02-03 14:04:40.857056');
INSERT INTO public.logs_sistema VALUES (453, 'debug', 'scheduler', 'Verificando rotinas agendadas...', NULL, NULL, NULL, '2026-02-03 14:05:40.935484');
INSERT INTO public.logs_sistema VALUES (454, 'error', 'scheduler', 'Erro na verificação: SQLSTATE[42P01]: Undefined table: 7 ERRO:  relação "rotinas" não existe
LINE 8:                     FROM rotinas r
                                 ^', NULL, NULL, NULL, '2026-02-03 14:05:40.940181');
INSERT INTO public.logs_sistema VALUES (455, 'debug', 'scheduler', 'Verificando rotinas agendadas...', NULL, NULL, NULL, '2026-02-03 14:06:41.084361');
INSERT INTO public.logs_sistema VALUES (456, 'error', 'scheduler', 'Erro na verificação: SQLSTATE[42P01]: Undefined table: 7 ERRO:  relação "rotinas" não existe
LINE 8:                     FROM rotinas r
                                 ^', NULL, NULL, NULL, '2026-02-03 14:06:41.119256');
INSERT INTO public.logs_sistema VALUES (457, 'debug', 'scheduler', 'Verificando rotinas agendadas...', NULL, NULL, NULL, '2026-02-03 14:07:41.260611');
INSERT INTO public.logs_sistema VALUES (458, 'error', 'scheduler', 'Erro na verificação: SQLSTATE[42P01]: Undefined table: 7 ERRO:  relação "rotinas" não existe
LINE 8:                     FROM rotinas r
                                 ^', NULL, NULL, NULL, '2026-02-03 14:07:41.26642');
INSERT INTO public.logs_sistema VALUES (459, 'debug', 'scheduler', 'Verificando rotinas agendadas...', NULL, NULL, NULL, '2026-02-03 14:08:41.373099');
INSERT INTO public.logs_sistema VALUES (460, 'error', 'scheduler', 'Erro na verificação: SQLSTATE[42P01]: Undefined table: 7 ERRO:  relação "rotinas" não existe
LINE 8:                     FROM rotinas r
                                 ^', NULL, NULL, NULL, '2026-02-03 14:08:41.383198');
INSERT INTO public.logs_sistema VALUES (461, 'debug', 'scheduler', 'Verificando rotinas agendadas...', NULL, NULL, NULL, '2026-02-03 14:09:41.525635');
INSERT INTO public.logs_sistema VALUES (462, 'error', 'scheduler', 'Erro na verificação: SQLSTATE[42P01]: Undefined table: 7 ERRO:  relação "rotinas" não existe
LINE 8:                     FROM rotinas r
                                 ^', NULL, NULL, NULL, '2026-02-03 14:09:41.530178');
INSERT INTO public.logs_sistema VALUES (463, 'debug', 'scheduler', 'Verificando rotinas agendadas...', NULL, NULL, NULL, '2026-02-03 14:10:41.612079');
INSERT INTO public.logs_sistema VALUES (464, 'error', 'scheduler', 'Erro na verificação: SQLSTATE[42P01]: Undefined table: 7 ERRO:  relação "rotinas" não existe
LINE 8:                     FROM rotinas r
                                 ^', NULL, NULL, NULL, '2026-02-03 14:10:41.617286');
INSERT INTO public.logs_sistema VALUES (465, 'debug', 'scheduler', 'Verificando rotinas agendadas...', NULL, NULL, NULL, '2026-02-03 14:11:41.701299');
INSERT INTO public.logs_sistema VALUES (466, 'error', 'scheduler', 'Erro na verificação: SQLSTATE[42P01]: Undefined table: 7 ERRO:  relação "rotinas" não existe
LINE 8:                     FROM rotinas r
                                 ^', NULL, NULL, NULL, '2026-02-03 14:11:41.703983');
INSERT INTO public.logs_sistema VALUES (467, 'debug', 'scheduler', 'Verificando rotinas agendadas...', NULL, NULL, NULL, '2026-02-03 14:12:41.827095');
INSERT INTO public.logs_sistema VALUES (468, 'error', 'scheduler', 'Erro na verificação: SQLSTATE[42P01]: Undefined table: 7 ERRO:  relação "rotinas" não existe
LINE 8:                     FROM rotinas r
                                 ^', NULL, NULL, NULL, '2026-02-03 14:12:41.830903');
INSERT INTO public.logs_sistema VALUES (469, 'debug', 'scheduler', 'Verificando rotinas agendadas...', NULL, NULL, NULL, '2026-02-03 14:13:41.92323');
INSERT INTO public.logs_sistema VALUES (470, 'error', 'scheduler', 'Erro na verificação: SQLSTATE[42P01]: Undefined table: 7 ERRO:  relação "rotinas" não existe
LINE 8:                     FROM rotinas r
                                 ^', NULL, NULL, NULL, '2026-02-03 14:13:41.929529');
INSERT INTO public.logs_sistema VALUES (471, 'debug', 'scheduler', 'Verificando rotinas agendadas...', NULL, NULL, NULL, '2026-02-03 14:14:42.022206');
INSERT INTO public.logs_sistema VALUES (472, 'error', 'scheduler', 'Erro na verificação: SQLSTATE[42P01]: Undefined table: 7 ERRO:  relação "rotinas" não existe
LINE 8:                     FROM rotinas r
                                 ^', NULL, NULL, NULL, '2026-02-03 14:14:42.026924');
INSERT INTO public.logs_sistema VALUES (473, 'debug', 'scheduler', 'Verificando rotinas agendadas...', NULL, NULL, NULL, '2026-02-03 14:15:42.094376');
INSERT INTO public.logs_sistema VALUES (474, 'error', 'scheduler', 'Erro na verificação: SQLSTATE[42P01]: Undefined table: 7 ERRO:  relação "rotinas" não existe
LINE 8:                     FROM rotinas r
                                 ^', NULL, NULL, NULL, '2026-02-03 14:15:42.122101');
INSERT INTO public.logs_sistema VALUES (475, 'debug', 'scheduler', 'Verificando rotinas agendadas...', NULL, NULL, NULL, '2026-02-03 14:16:42.195261');
INSERT INTO public.logs_sistema VALUES (476, 'error', 'scheduler', 'Erro na verificação: SQLSTATE[42P01]: Undefined table: 7 ERRO:  relação "rotinas" não existe
LINE 8:                     FROM rotinas r
                                 ^', NULL, NULL, NULL, '2026-02-03 14:16:42.199108');
INSERT INTO public.logs_sistema VALUES (477, 'debug', 'scheduler', 'Verificando rotinas agendadas...', NULL, NULL, NULL, '2026-02-03 14:17:42.279327');
INSERT INTO public.logs_sistema VALUES (478, 'error', 'scheduler', 'Erro na verificação: SQLSTATE[42P01]: Undefined table: 7 ERRO:  relação "rotinas" não existe
LINE 8:                     FROM rotinas r
                                 ^', NULL, NULL, NULL, '2026-02-03 14:17:42.298719');
INSERT INTO public.logs_sistema VALUES (479, 'debug', 'scheduler', 'Verificando rotinas agendadas...', NULL, NULL, NULL, '2026-02-03 14:18:42.364851');
INSERT INTO public.logs_sistema VALUES (480, 'error', 'scheduler', 'Erro na verificação: SQLSTATE[42P01]: Undefined table: 7 ERRO:  relação "rotinas" não existe
LINE 8:                     FROM rotinas r
                                 ^', NULL, NULL, NULL, '2026-02-03 14:18:42.368841');
INSERT INTO public.logs_sistema VALUES (481, 'debug', 'scheduler', 'Verificando rotinas agendadas...', NULL, NULL, NULL, '2026-02-03 14:19:42.431231');
INSERT INTO public.logs_sistema VALUES (482, 'error', 'scheduler', 'Erro na verificação: SQLSTATE[42P01]: Undefined table: 7 ERRO:  relação "rotinas" não existe
LINE 8:                     FROM rotinas r
                                 ^', NULL, NULL, NULL, '2026-02-03 14:19:42.466073');
INSERT INTO public.logs_sistema VALUES (483, 'debug', 'scheduler', 'Verificando rotinas agendadas...', NULL, NULL, NULL, '2026-02-03 14:20:42.580586');
INSERT INTO public.logs_sistema VALUES (484, 'error', 'scheduler', 'Erro na verificação: SQLSTATE[42P01]: Undefined table: 7 ERRO:  relação "rotinas" não existe
LINE 8:                     FROM rotinas r
                                 ^', NULL, NULL, NULL, '2026-02-03 14:20:42.615823');
INSERT INTO public.logs_sistema VALUES (485, 'debug', 'scheduler', 'Verificando rotinas agendadas...', NULL, NULL, NULL, '2026-02-03 14:21:42.743644');
INSERT INTO public.logs_sistema VALUES (486, 'error', 'scheduler', 'Erro na verificação: SQLSTATE[42P01]: Undefined table: 7 ERRO:  relação "rotinas" não existe
LINE 8:                     FROM rotinas r
                                 ^', NULL, NULL, NULL, '2026-02-03 14:21:42.797208');
INSERT INTO public.logs_sistema VALUES (487, 'debug', 'scheduler', 'Verificando rotinas agendadas...', NULL, NULL, NULL, '2026-02-03 14:22:42.910889');
INSERT INTO public.logs_sistema VALUES (488, 'error', 'scheduler', 'Erro na verificação: SQLSTATE[42P01]: Undefined table: 7 ERRO:  relação "rotinas" não existe
LINE 8:                     FROM rotinas r
                                 ^', NULL, NULL, NULL, '2026-02-03 14:22:42.916996');
INSERT INTO public.logs_sistema VALUES (489, 'debug', 'scheduler', 'Verificando rotinas agendadas...', NULL, NULL, NULL, '2026-02-03 14:23:43.01303');
INSERT INTO public.logs_sistema VALUES (490, 'error', 'scheduler', 'Erro na verificação: SQLSTATE[42P01]: Undefined table: 7 ERRO:  relação "rotinas" não existe
LINE 8:                     FROM rotinas r
                                 ^', NULL, NULL, NULL, '2026-02-03 14:23:43.017788');
INSERT INTO public.logs_sistema VALUES (491, 'debug', 'scheduler', 'Verificando rotinas agendadas...', NULL, NULL, NULL, '2026-02-03 14:24:43.175027');
INSERT INTO public.logs_sistema VALUES (492, 'error', 'scheduler', 'Erro na verificação: SQLSTATE[42P01]: Undefined table: 7 ERRO:  relação "rotinas" não existe
LINE 8:                     FROM rotinas r
                                 ^', NULL, NULL, NULL, '2026-02-03 14:24:43.210598');
INSERT INTO public.logs_sistema VALUES (493, 'debug', 'scheduler', 'Verificando rotinas agendadas...', NULL, NULL, NULL, '2026-02-03 14:25:43.305026');
INSERT INTO public.logs_sistema VALUES (494, 'error', 'scheduler', 'Erro na verificação: SQLSTATE[42P01]: Undefined table: 7 ERRO:  relação "rotinas" não existe
LINE 8:                     FROM rotinas r
                                 ^', NULL, NULL, NULL, '2026-02-03 14:25:43.309229');
INSERT INTO public.logs_sistema VALUES (495, 'debug', 'scheduler', 'Verificando rotinas agendadas...', NULL, NULL, NULL, '2026-02-03 14:26:43.400879');
INSERT INTO public.logs_sistema VALUES (496, 'error', 'scheduler', 'Erro na verificação: SQLSTATE[42P01]: Undefined table: 7 ERRO:  relação "rotinas" não existe
LINE 8:                     FROM rotinas r
                                 ^', NULL, NULL, NULL, '2026-02-03 14:26:43.435591');
INSERT INTO public.logs_sistema VALUES (497, 'debug', 'scheduler', 'Verificando rotinas agendadas...', NULL, NULL, NULL, '2026-02-03 14:27:43.532472');
INSERT INTO public.logs_sistema VALUES (498, 'error', 'scheduler', 'Erro na verificação: SQLSTATE[42P01]: Undefined table: 7 ERRO:  relação "rotinas" não existe
LINE 8:                     FROM rotinas r
                                 ^', NULL, NULL, NULL, '2026-02-03 14:27:43.535758');
INSERT INTO public.logs_sistema VALUES (500, 'error', 'scheduler', 'Erro na verificação: SQLSTATE[42P01]: Undefined table: 7 ERRO:  relação "rotinas" não existe
LINE 8:                     FROM rotinas r
                                 ^', NULL, NULL, NULL, '2026-02-03 14:28:43.64009');
INSERT INTO public.logs_sistema VALUES (501, 'debug', 'scheduler', 'Verificando rotinas agendadas...', NULL, NULL, NULL, '2026-02-03 14:29:43.733149');
INSERT INTO public.logs_sistema VALUES (502, 'error', 'scheduler', 'Erro na verificação: SQLSTATE[42P01]: Undefined table: 7 ERRO:  relação "rotinas" não existe
LINE 8:                     FROM rotinas r
                                 ^', NULL, NULL, NULL, '2026-02-03 14:29:43.736414');
INSERT INTO public.logs_sistema VALUES (503, 'debug', 'scheduler', 'Verificando rotinas agendadas...', NULL, NULL, NULL, '2026-02-03 14:30:43.862103');
INSERT INTO public.logs_sistema VALUES (504, 'error', 'scheduler', 'Erro na verificação: SQLSTATE[42P01]: Undefined table: 7 ERRO:  relação "rotinas" não existe
LINE 8:                     FROM rotinas r
                                 ^', NULL, NULL, NULL, '2026-02-03 14:30:43.883936');
INSERT INTO public.logs_sistema VALUES (505, 'debug', 'scheduler', 'Verificando rotinas agendadas...', NULL, NULL, NULL, '2026-02-03 14:31:43.952369');
INSERT INTO public.logs_sistema VALUES (506, 'error', 'scheduler', 'Erro na verificação: SQLSTATE[42P01]: Undefined table: 7 ERRO:  relação "rotinas" não existe
LINE 8:                     FROM rotinas r
                                 ^', NULL, NULL, NULL, '2026-02-03 14:31:43.968048');
INSERT INTO public.logs_sistema VALUES (507, 'debug', 'scheduler', 'Verificando rotinas agendadas...', NULL, NULL, NULL, '2026-02-03 14:32:44.089243');
INSERT INTO public.logs_sistema VALUES (508, 'error', 'scheduler', 'Erro na verificação: SQLSTATE[42P01]: Undefined table: 7 ERRO:  relação "rotinas" não existe
LINE 8:                     FROM rotinas r
                                 ^', NULL, NULL, NULL, '2026-02-03 14:32:44.093561');
INSERT INTO public.logs_sistema VALUES (509, 'debug', 'scheduler', 'Verificando rotinas agendadas...', NULL, NULL, NULL, '2026-02-03 14:33:44.210582');
INSERT INTO public.logs_sistema VALUES (510, 'error', 'scheduler', 'Erro na verificação: SQLSTATE[42P01]: Undefined table: 7 ERRO:  relação "rotinas" não existe
LINE 8:                     FROM rotinas r
                                 ^', NULL, NULL, NULL, '2026-02-03 14:33:44.214017');
INSERT INTO public.logs_sistema VALUES (511, 'debug', 'scheduler', 'Verificando rotinas agendadas...', NULL, NULL, NULL, '2026-02-03 14:34:44.292687');
INSERT INTO public.logs_sistema VALUES (512, 'error', 'scheduler', 'Erro na verificação: SQLSTATE[42P01]: Undefined table: 7 ERRO:  relação "rotinas" não existe
LINE 8:                     FROM rotinas r
                                 ^', NULL, NULL, NULL, '2026-02-03 14:34:44.295296');
INSERT INTO public.logs_sistema VALUES (513, 'debug', 'scheduler', 'Verificando rotinas agendadas...', NULL, NULL, NULL, '2026-02-03 14:35:44.39529');
INSERT INTO public.logs_sistema VALUES (514, 'error', 'scheduler', 'Erro na verificação: SQLSTATE[42P01]: Undefined table: 7 ERRO:  relação "rotinas" não existe
LINE 8:                     FROM rotinas r
                                 ^', NULL, NULL, NULL, '2026-02-03 14:35:44.400311');
INSERT INTO public.logs_sistema VALUES (515, 'debug', 'scheduler', 'Verificando rotinas agendadas...', NULL, NULL, NULL, '2026-02-03 14:36:44.485018');
INSERT INTO public.logs_sistema VALUES (516, 'error', 'scheduler', 'Erro na verificação: SQLSTATE[42P01]: Undefined table: 7 ERRO:  relação "rotinas" não existe
LINE 8:                     FROM rotinas r
                                 ^', NULL, NULL, NULL, '2026-02-03 14:36:44.48895');
INSERT INTO public.logs_sistema VALUES (517, 'debug', 'scheduler', 'Verificando rotinas agendadas...', NULL, NULL, NULL, '2026-02-03 14:37:44.546171');
INSERT INTO public.logs_sistema VALUES (518, 'error', 'scheduler', 'Erro na verificação: SQLSTATE[42P01]: Undefined table: 7 ERRO:  relação "rotinas" não existe
LINE 8:                     FROM rotinas r
                                 ^', NULL, NULL, NULL, '2026-02-03 14:37:44.549667');
INSERT INTO public.logs_sistema VALUES (519, 'debug', 'scheduler', 'Verificando rotinas agendadas...', NULL, NULL, NULL, '2026-02-03 14:38:44.649683');
INSERT INTO public.logs_sistema VALUES (520, 'error', 'scheduler', 'Erro na verificação: SQLSTATE[42P01]: Undefined table: 7 ERRO:  relação "rotinas" não existe
LINE 8:                     FROM rotinas r
                                 ^', NULL, NULL, NULL, '2026-02-03 14:38:44.658917');
INSERT INTO public.logs_sistema VALUES (521, 'debug', 'scheduler', 'Verificando rotinas agendadas...', NULL, NULL, NULL, '2026-02-03 14:39:44.76549');
INSERT INTO public.logs_sistema VALUES (522, 'error', 'scheduler', 'Erro na verificação: SQLSTATE[42P01]: Undefined table: 7 ERRO:  relação "rotinas" não existe
LINE 8:                     FROM rotinas r
                                 ^', NULL, NULL, NULL, '2026-02-03 14:39:44.769469');
INSERT INTO public.logs_sistema VALUES (523, 'debug', 'scheduler', 'Verificando rotinas agendadas...', NULL, NULL, NULL, '2026-02-03 14:40:44.877572');
INSERT INTO public.logs_sistema VALUES (524, 'error', 'scheduler', 'Erro na verificação: SQLSTATE[42P01]: Undefined table: 7 ERRO:  relação "rotinas" não existe
LINE 8:                     FROM rotinas r
                                 ^', NULL, NULL, NULL, '2026-02-03 14:40:44.881034');
INSERT INTO public.logs_sistema VALUES (525, 'debug', 'scheduler', 'Verificando rotinas agendadas...', NULL, NULL, NULL, '2026-02-03 14:41:44.988305');
INSERT INTO public.logs_sistema VALUES (526, 'error', 'scheduler', 'Erro na verificação: SQLSTATE[42P01]: Undefined table: 7 ERRO:  relação "rotinas" não existe
LINE 8:                     FROM rotinas r
                                 ^', NULL, NULL, NULL, '2026-02-03 14:41:44.991517');
INSERT INTO public.logs_sistema VALUES (527, 'debug', 'scheduler', 'Verificando rotinas agendadas...', NULL, NULL, NULL, '2026-02-03 14:42:45.08464');
INSERT INTO public.logs_sistema VALUES (528, 'error', 'scheduler', 'Erro na verificação: SQLSTATE[42P01]: Undefined table: 7 ERRO:  relação "rotinas" não existe
LINE 8:                     FROM rotinas r
                                 ^', NULL, NULL, NULL, '2026-02-03 14:42:45.088948');
INSERT INTO public.logs_sistema VALUES (529, 'debug', 'scheduler', 'Verificando rotinas agendadas...', NULL, NULL, NULL, '2026-02-03 14:43:45.168029');
INSERT INTO public.logs_sistema VALUES (530, 'error', 'scheduler', 'Erro na verificação: SQLSTATE[42P01]: Undefined table: 7 ERRO:  relação "rotinas" não existe
LINE 8:                     FROM rotinas r
                                 ^', NULL, NULL, NULL, '2026-02-03 14:43:45.170949');
INSERT INTO public.logs_sistema VALUES (531, 'debug', 'scheduler', 'Verificando rotinas agendadas...', NULL, NULL, NULL, '2026-02-03 14:44:45.271877');
INSERT INTO public.logs_sistema VALUES (532, 'error', 'scheduler', 'Erro na verificação: SQLSTATE[42P01]: Undefined table: 7 ERRO:  relação "rotinas" não existe
LINE 8:                     FROM rotinas r
                                 ^', NULL, NULL, NULL, '2026-02-03 14:44:45.306746');
INSERT INTO public.logs_sistema VALUES (533, 'debug', 'scheduler', 'Verificando rotinas agendadas...', NULL, NULL, NULL, '2026-02-03 14:45:45.390646');
INSERT INTO public.logs_sistema VALUES (534, 'error', 'scheduler', 'Erro na verificação: SQLSTATE[42P01]: Undefined table: 7 ERRO:  relação "rotinas" não existe
LINE 8:                     FROM rotinas r
                                 ^', NULL, NULL, NULL, '2026-02-03 14:45:45.427516');
INSERT INTO public.logs_sistema VALUES (535, 'debug', 'scheduler', 'Verificando rotinas agendadas...', NULL, NULL, NULL, '2026-02-03 14:46:45.517719');
INSERT INTO public.logs_sistema VALUES (536, 'error', 'scheduler', 'Erro na verificação: SQLSTATE[42P01]: Undefined table: 7 ERRO:  relação "rotinas" não existe
LINE 8:                     FROM rotinas r
                                 ^', NULL, NULL, NULL, '2026-02-03 14:46:45.520826');
INSERT INTO public.logs_sistema VALUES (537, 'debug', 'scheduler', 'Verificando rotinas agendadas...', NULL, NULL, NULL, '2026-02-03 14:47:45.597711');
INSERT INTO public.logs_sistema VALUES (538, 'error', 'scheduler', 'Erro na verificação: SQLSTATE[42P01]: Undefined table: 7 ERRO:  relação "rotinas" não existe
LINE 8:                     FROM rotinas r
                                 ^', NULL, NULL, NULL, '2026-02-03 14:47:45.600662');
INSERT INTO public.logs_sistema VALUES (539, 'debug', 'scheduler', 'Verificando rotinas agendadas...', NULL, NULL, NULL, '2026-02-03 14:48:45.717993');
INSERT INTO public.logs_sistema VALUES (540, 'error', 'scheduler', 'Erro na verificação: SQLSTATE[42P01]: Undefined table: 7 ERRO:  relação "rotinas" não existe
LINE 8:                     FROM rotinas r
                                 ^', NULL, NULL, NULL, '2026-02-03 14:48:45.722529');
INSERT INTO public.logs_sistema VALUES (541, 'debug', 'scheduler', 'Verificando rotinas agendadas...', NULL, NULL, NULL, '2026-02-03 14:49:45.871868');
INSERT INTO public.logs_sistema VALUES (542, 'error', 'scheduler', 'Erro na verificação: SQLSTATE[42P01]: Undefined table: 7 ERRO:  relação "rotinas" não existe
LINE 8:                     FROM rotinas r
                                 ^', NULL, NULL, NULL, '2026-02-03 14:49:45.875966');
INSERT INTO public.logs_sistema VALUES (543, 'debug', 'scheduler', 'Verificando rotinas agendadas...', NULL, NULL, NULL, '2026-02-03 14:50:45.960103');
INSERT INTO public.logs_sistema VALUES (544, 'error', 'scheduler', 'Erro na verificação: SQLSTATE[42P01]: Undefined table: 7 ERRO:  relação "rotinas" não existe
LINE 8:                     FROM rotinas r
                                 ^', NULL, NULL, NULL, '2026-02-03 14:50:45.966067');
INSERT INTO public.logs_sistema VALUES (545, 'debug', 'scheduler', 'Verificando rotinas agendadas...', NULL, NULL, NULL, '2026-02-03 14:51:46.048492');
INSERT INTO public.logs_sistema VALUES (546, 'error', 'scheduler', 'Erro na verificação: SQLSTATE[42P01]: Undefined table: 7 ERRO:  relação "rotinas" não existe
LINE 8:                     FROM rotinas r
                                 ^', NULL, NULL, NULL, '2026-02-03 14:51:46.083276');
INSERT INTO public.logs_sistema VALUES (547, 'debug', 'scheduler', 'Verificando rotinas agendadas...', NULL, NULL, NULL, '2026-02-03 14:52:46.195478');
INSERT INTO public.logs_sistema VALUES (548, 'error', 'scheduler', 'Erro na verificação: SQLSTATE[42P01]: Undefined table: 7 ERRO:  relação "rotinas" não existe
LINE 8:                     FROM rotinas r
                                 ^', NULL, NULL, NULL, '2026-02-03 14:52:46.213885');
INSERT INTO public.logs_sistema VALUES (549, 'debug', 'scheduler', 'Verificando rotinas agendadas...', NULL, NULL, NULL, '2026-02-03 14:53:46.351505');
INSERT INTO public.logs_sistema VALUES (550, 'error', 'scheduler', 'Erro na verificação: SQLSTATE[42P01]: Undefined table: 7 ERRO:  relação "rotinas" não existe
LINE 8:                     FROM rotinas r
                                 ^', NULL, NULL, NULL, '2026-02-03 14:53:46.35473');
INSERT INTO public.logs_sistema VALUES (551, 'debug', 'scheduler', 'Verificando rotinas agendadas...', NULL, NULL, NULL, '2026-02-03 14:54:46.468891');
INSERT INTO public.logs_sistema VALUES (552, 'error', 'scheduler', 'Erro na verificação: SQLSTATE[42P01]: Undefined table: 7 ERRO:  relação "rotinas" não existe
LINE 8:                     FROM rotinas r
                                 ^', NULL, NULL, NULL, '2026-02-03 14:54:46.471709');
INSERT INTO public.logs_sistema VALUES (553, 'debug', 'scheduler', 'Verificando rotinas agendadas...', NULL, NULL, NULL, '2026-02-03 14:55:46.613118');
INSERT INTO public.logs_sistema VALUES (554, 'error', 'scheduler', 'Erro na verificação: SQLSTATE[42P01]: Undefined table: 7 ERRO:  relação "rotinas" não existe
LINE 8:                     FROM rotinas r
                                 ^', NULL, NULL, NULL, '2026-02-03 14:55:46.620695');
INSERT INTO public.logs_sistema VALUES (555, 'debug', 'scheduler', 'Verificando rotinas agendadas...', NULL, NULL, NULL, '2026-02-03 14:56:46.721087');
INSERT INTO public.logs_sistema VALUES (556, 'error', 'scheduler', 'Erro na verificação: SQLSTATE[42P01]: Undefined table: 7 ERRO:  relação "rotinas" não existe
LINE 8:                     FROM rotinas r
                                 ^', NULL, NULL, NULL, '2026-02-03 14:56:46.724621');
INSERT INTO public.logs_sistema VALUES (557, 'debug', 'scheduler', 'Verificando rotinas agendadas...', NULL, NULL, NULL, '2026-02-03 14:57:46.788885');
INSERT INTO public.logs_sistema VALUES (558, 'error', 'scheduler', 'Erro na verificação: SQLSTATE[42P01]: Undefined table: 7 ERRO:  relação "rotinas" não existe
LINE 8:                     FROM rotinas r
                                 ^', NULL, NULL, NULL, '2026-02-03 14:57:46.824846');
INSERT INTO public.logs_sistema VALUES (559, 'debug', 'scheduler', 'Verificando rotinas agendadas...', NULL, NULL, NULL, '2026-02-03 14:58:46.884995');
INSERT INTO public.logs_sistema VALUES (560, 'error', 'scheduler', 'Erro na verificação: SQLSTATE[42P01]: Undefined table: 7 ERRO:  relação "rotinas" não existe
LINE 8:                     FROM rotinas r
                                 ^', NULL, NULL, NULL, '2026-02-03 14:58:46.889285');
INSERT INTO public.logs_sistema VALUES (561, 'debug', 'scheduler', 'Verificando rotinas agendadas...', NULL, NULL, NULL, '2026-02-03 14:59:46.943921');
INSERT INTO public.logs_sistema VALUES (562, 'error', 'scheduler', 'Erro na verificação: SQLSTATE[42P01]: Undefined table: 7 ERRO:  relação "rotinas" não existe
LINE 8:                     FROM rotinas r
                                 ^', NULL, NULL, NULL, '2026-02-03 14:59:46.94668');
INSERT INTO public.logs_sistema VALUES (563, 'debug', 'scheduler', 'Verificando rotinas agendadas...', NULL, NULL, NULL, '2026-02-03 15:00:47.057294');
INSERT INTO public.logs_sistema VALUES (564, 'error', 'scheduler', 'Erro na verificação: SQLSTATE[42P01]: Undefined table: 7 ERRO:  relação "rotinas" não existe
LINE 8:                     FROM rotinas r
                                 ^', NULL, NULL, NULL, '2026-02-03 15:00:47.061599');
INSERT INTO public.logs_sistema VALUES (565, 'debug', 'scheduler', 'Verificando rotinas agendadas...', NULL, NULL, NULL, '2026-02-03 15:01:47.184284');
INSERT INTO public.logs_sistema VALUES (566, 'error', 'scheduler', 'Erro na verificação: SQLSTATE[42P01]: Undefined table: 7 ERRO:  relação "rotinas" não existe
LINE 8:                     FROM rotinas r
                                 ^', NULL, NULL, NULL, '2026-02-03 15:01:47.218943');
INSERT INTO public.logs_sistema VALUES (567, 'debug', 'scheduler', 'Verificando rotinas agendadas...', NULL, NULL, NULL, '2026-02-03 15:02:47.304651');
INSERT INTO public.logs_sistema VALUES (568, 'error', 'scheduler', 'Erro na verificação: SQLSTATE[42P01]: Undefined table: 7 ERRO:  relação "rotinas" não existe
LINE 8:                     FROM rotinas r
                                 ^', NULL, NULL, NULL, '2026-02-03 15:02:47.307927');
INSERT INTO public.logs_sistema VALUES (569, 'debug', 'scheduler', 'Verificando rotinas agendadas...', NULL, NULL, NULL, '2026-02-03 15:03:47.426202');
INSERT INTO public.logs_sistema VALUES (570, 'error', 'scheduler', 'Erro na verificação: SQLSTATE[42P01]: Undefined table: 7 ERRO:  relação "rotinas" não existe
LINE 8:                     FROM rotinas r
                                 ^', NULL, NULL, NULL, '2026-02-03 15:03:47.460951');
INSERT INTO public.logs_sistema VALUES (571, 'debug', 'scheduler', 'Verificando rotinas agendadas...', NULL, NULL, NULL, '2026-02-03 15:04:47.580074');
INSERT INTO public.logs_sistema VALUES (572, 'error', 'scheduler', 'Erro na verificação: SQLSTATE[42P01]: Undefined table: 7 ERRO:  relação "rotinas" não existe
LINE 8:                     FROM rotinas r
                                 ^', NULL, NULL, NULL, '2026-02-03 15:04:47.582621');
INSERT INTO public.logs_sistema VALUES (573, 'debug', 'scheduler', 'Verificando rotinas agendadas...', NULL, NULL, NULL, '2026-02-03 15:05:47.715651');
INSERT INTO public.logs_sistema VALUES (574, 'error', 'scheduler', 'Erro na verificação: SQLSTATE[42P01]: Undefined table: 7 ERRO:  relação "rotinas" não existe
LINE 8:                     FROM rotinas r
                                 ^', NULL, NULL, NULL, '2026-02-03 15:05:47.719762');
INSERT INTO public.logs_sistema VALUES (575, 'debug', 'scheduler', 'Verificando rotinas agendadas...', NULL, NULL, NULL, '2026-02-03 15:06:47.79874');
INSERT INTO public.logs_sistema VALUES (576, 'error', 'scheduler', 'Erro na verificação: SQLSTATE[42P01]: Undefined table: 7 ERRO:  relação "rotinas" não existe
LINE 8:                     FROM rotinas r
                                 ^', NULL, NULL, NULL, '2026-02-03 15:06:47.833211');
INSERT INTO public.logs_sistema VALUES (577, 'debug', 'scheduler', 'Verificando rotinas agendadas...', NULL, NULL, NULL, '2026-02-03 15:07:47.943985');
INSERT INTO public.logs_sistema VALUES (578, 'error', 'scheduler', 'Erro na verificação: SQLSTATE[42P01]: Undefined table: 7 ERRO:  relação "rotinas" não existe
LINE 8:                     FROM rotinas r
                                 ^', NULL, NULL, NULL, '2026-02-03 15:07:47.946395');
INSERT INTO public.logs_sistema VALUES (579, 'debug', 'scheduler', 'Verificando rotinas agendadas...', NULL, NULL, NULL, '2026-02-03 15:08:48.054772');
INSERT INTO public.logs_sistema VALUES (580, 'error', 'scheduler', 'Erro na verificação: SQLSTATE[42P01]: Undefined table: 7 ERRO:  relação "rotinas" não existe
LINE 8:                     FROM rotinas r
                                 ^', NULL, NULL, NULL, '2026-02-03 15:08:48.089648');
INSERT INTO public.logs_sistema VALUES (581, 'debug', 'scheduler', 'Verificando rotinas agendadas...', NULL, NULL, NULL, '2026-02-03 15:09:48.201835');
INSERT INTO public.logs_sistema VALUES (582, 'error', 'scheduler', 'Erro na verificação: SQLSTATE[42P01]: Undefined table: 7 ERRO:  relação "rotinas" não existe
LINE 8:                     FROM rotinas r
                                 ^', NULL, NULL, NULL, '2026-02-03 15:09:48.235865');
INSERT INTO public.logs_sistema VALUES (583, 'debug', 'scheduler', 'Verificando rotinas agendadas...', NULL, NULL, NULL, '2026-02-03 15:10:48.333836');
INSERT INTO public.logs_sistema VALUES (584, 'error', 'scheduler', 'Erro na verificação: SQLSTATE[42P01]: Undefined table: 7 ERRO:  relação "rotinas" não existe
LINE 8:                     FROM rotinas r
                                 ^', NULL, NULL, NULL, '2026-02-03 15:10:48.336646');
INSERT INTO public.logs_sistema VALUES (585, 'debug', 'scheduler', 'Verificando rotinas agendadas...', NULL, NULL, NULL, '2026-02-03 15:11:48.458384');
INSERT INTO public.logs_sistema VALUES (586, 'error', 'scheduler', 'Erro na verificação: SQLSTATE[42P01]: Undefined table: 7 ERRO:  relação "rotinas" não existe
LINE 8:                     FROM rotinas r
                                 ^', NULL, NULL, NULL, '2026-02-03 15:11:48.473258');
INSERT INTO public.logs_sistema VALUES (587, 'debug', 'scheduler', 'Verificando rotinas agendadas...', NULL, NULL, NULL, '2026-02-03 15:12:48.583792');
INSERT INTO public.logs_sistema VALUES (588, 'error', 'scheduler', 'Erro na verificação: SQLSTATE[42P01]: Undefined table: 7 ERRO:  relação "rotinas" não existe
LINE 8:                     FROM rotinas r
                                 ^', NULL, NULL, NULL, '2026-02-03 15:12:48.618064');
INSERT INTO public.logs_sistema VALUES (589, 'debug', 'scheduler', 'Verificando rotinas agendadas...', NULL, NULL, NULL, '2026-02-03 15:13:48.717261');
INSERT INTO public.logs_sistema VALUES (590, 'error', 'scheduler', 'Erro na verificação: SQLSTATE[42P01]: Undefined table: 7 ERRO:  relação "rotinas" não existe
LINE 8:                     FROM rotinas r
                                 ^', NULL, NULL, NULL, '2026-02-03 15:13:48.719827');
INSERT INTO public.logs_sistema VALUES (591, 'debug', 'scheduler', 'Verificando rotinas agendadas...', NULL, NULL, NULL, '2026-02-03 15:14:48.850616');
INSERT INTO public.logs_sistema VALUES (592, 'error', 'scheduler', 'Erro na verificação: SQLSTATE[42P01]: Undefined table: 7 ERRO:  relação "rotinas" não existe
LINE 8:                     FROM rotinas r
                                 ^', NULL, NULL, NULL, '2026-02-03 15:14:48.885108');
INSERT INTO public.logs_sistema VALUES (593, 'debug', 'scheduler', 'Verificando rotinas agendadas...', NULL, NULL, NULL, '2026-02-03 15:15:48.989458');
INSERT INTO public.logs_sistema VALUES (594, 'error', 'scheduler', 'Erro na verificação: SQLSTATE[42P01]: Undefined table: 7 ERRO:  relação "rotinas" não existe
LINE 8:                     FROM rotinas r
                                 ^', NULL, NULL, NULL, '2026-02-03 15:15:48.993856');
INSERT INTO public.logs_sistema VALUES (595, 'debug', 'scheduler', 'Verificando rotinas agendadas...', NULL, NULL, NULL, '2026-02-03 15:16:49.106844');
INSERT INTO public.logs_sistema VALUES (596, 'error', 'scheduler', 'Erro na verificação: SQLSTATE[42P01]: Undefined table: 7 ERRO:  relação "rotinas" não existe
LINE 8:                     FROM rotinas r
                                 ^', NULL, NULL, NULL, '2026-02-03 15:16:49.110059');
INSERT INTO public.logs_sistema VALUES (597, 'debug', 'scheduler', 'Verificando rotinas agendadas...', NULL, NULL, NULL, '2026-02-03 15:17:49.190756');
INSERT INTO public.logs_sistema VALUES (598, 'error', 'scheduler', 'Erro na verificação: SQLSTATE[42P01]: Undefined table: 7 ERRO:  relação "rotinas" não existe
LINE 8:                     FROM rotinas r
                                 ^', NULL, NULL, NULL, '2026-02-03 15:17:49.193792');
INSERT INTO public.logs_sistema VALUES (599, 'debug', 'scheduler', 'Verificando rotinas agendadas...', NULL, NULL, NULL, '2026-02-03 15:18:49.317398');
INSERT INTO public.logs_sistema VALUES (600, 'error', 'scheduler', 'Erro na verificação: SQLSTATE[42P01]: Undefined table: 7 ERRO:  relação "rotinas" não existe
LINE 8:                     FROM rotinas r
                                 ^', NULL, NULL, NULL, '2026-02-03 15:18:49.325996');
INSERT INTO public.logs_sistema VALUES (601, 'debug', 'scheduler', 'Verificando rotinas agendadas...', NULL, NULL, NULL, '2026-02-03 15:19:49.428786');
INSERT INTO public.logs_sistema VALUES (602, 'error', 'scheduler', 'Erro na verificação: SQLSTATE[42P01]: Undefined table: 7 ERRO:  relação "rotinas" não existe
LINE 8:                     FROM rotinas r
                                 ^', NULL, NULL, NULL, '2026-02-03 15:19:49.431234');
INSERT INTO public.logs_sistema VALUES (603, 'debug', 'scheduler', 'Verificando rotinas agendadas...', NULL, NULL, NULL, '2026-02-03 15:20:49.514881');
INSERT INTO public.logs_sistema VALUES (604, 'error', 'scheduler', 'Erro na verificação: SQLSTATE[42P01]: Undefined table: 7 ERRO:  relação "rotinas" não existe
LINE 8:                     FROM rotinas r
                                 ^', NULL, NULL, NULL, '2026-02-03 15:20:49.518639');
INSERT INTO public.logs_sistema VALUES (605, 'debug', 'scheduler', 'Verificando rotinas agendadas...', NULL, NULL, NULL, '2026-02-03 15:21:49.576669');
INSERT INTO public.logs_sistema VALUES (606, 'error', 'scheduler', 'Erro na verificação: SQLSTATE[42P01]: Undefined table: 7 ERRO:  relação "rotinas" não existe
LINE 8:                     FROM rotinas r
                                 ^', NULL, NULL, NULL, '2026-02-03 15:21:49.611696');
INSERT INTO public.logs_sistema VALUES (607, 'info', 'scheduler', 'Scheduler Worker iniciado (PID: 15928)', NULL, NULL, NULL, '2026-02-03 15:22:11.551311');
INSERT INTO public.logs_sistema VALUES (608, 'debug', 'scheduler', 'Verificando rotinas agendadas...', NULL, NULL, NULL, '2026-02-03 15:22:11.559134');
INSERT INTO public.logs_sistema VALUES (609, 'info', 'scheduler', 'Scheduler Worker iniciado (PID: 20284)', NULL, NULL, NULL, '2026-02-03 15:24:02.539289');
INSERT INTO public.logs_sistema VALUES (610, 'debug', 'scheduler', 'Verificando rotinas agendadas...', NULL, NULL, NULL, '2026-02-03 15:24:02.55558');
INSERT INTO public.logs_sistema VALUES (611, 'info', 'scheduler', 'Scheduler Worker iniciado (PID: 18208)', NULL, NULL, NULL, '2026-02-03 15:26:53.911839');
INSERT INTO public.logs_sistema VALUES (612, 'debug', 'scheduler', 'Verificando rotinas agendadas...', NULL, NULL, NULL, '2026-02-03 15:26:53.923661');
INSERT INTO public.logs_sistema VALUES (613, 'info', 'scheduler', '1 rotina(s) para executar', NULL, NULL, NULL, '2026-02-03 15:26:53.933398');
INSERT INTO public.logs_sistema VALUES (614, 'info', 'scheduler', 'Iniciando execução: Rotina Teste Scheduler (ID: 29)', NULL, NULL, NULL, '2026-02-03 15:26:53.937676');
INSERT INTO public.logs_sistema VALUES (615, 'error', 'scheduler', 'Erro na verificação: SQLSTATE[42P01]: Undefined table: 7 ERRO:  relação "historico_execucoes" não existe
LINE 1: INSERT INTO historico_execucoes (rotina_id, inicio, status)
                    ^', NULL, NULL, NULL, '2026-02-03 15:26:53.9434');
INSERT INTO public.logs_sistema VALUES (616, 'info', 'scheduler', 'Scheduler Worker iniciado (PID: 9200)', NULL, NULL, NULL, '2026-02-03 15:28:01.422076');
INSERT INTO public.logs_sistema VALUES (617, 'debug', 'scheduler', 'Verificando rotinas agendadas...', NULL, NULL, NULL, '2026-02-03 15:28:01.429105');
INSERT INTO public.logs_sistema VALUES (618, 'info', 'scheduler', '1 rotina(s) para executar', NULL, NULL, NULL, '2026-02-03 15:28:01.437065');
INSERT INTO public.logs_sistema VALUES (619, 'info', 'scheduler', 'Iniciando execução: Rotina Teste Scheduler (ID: 29)', NULL, NULL, NULL, '2026-02-03 15:28:01.441397');
INSERT INTO public.logs_sistema VALUES (620, 'error', 'scheduler', 'ERRO em Rotina Teste Scheduler: Tipo de banco não suportado: postgres', NULL, NULL, NULL, '2026-02-03 15:28:01.44553');
INSERT INTO public.logs_sistema VALUES (621, 'error', 'scheduler', 'Erro na verificação: SQLSTATE[42P01]: Undefined table: 7 ERRO:  relação "rotinas" não existe
LINE 1: UPDATE rotinas 
               ^', NULL, NULL, NULL, '2026-02-03 15:28:01.45201');
INSERT INTO public.logs_sistema VALUES (622, 'info', 'scheduler', 'Scheduler Worker iniciado (PID: 30516)', NULL, NULL, NULL, '2026-02-03 15:29:36.145289');
INSERT INTO public.logs_sistema VALUES (623, 'debug', 'scheduler', 'Verificando rotinas agendadas...', NULL, NULL, NULL, '2026-02-03 15:29:36.164595');
INSERT INTO public.logs_sistema VALUES (624, 'info', 'scheduler', '1 rotina(s) para executar', NULL, NULL, NULL, '2026-02-03 15:29:36.181412');
INSERT INTO public.logs_sistema VALUES (625, 'info', 'scheduler', 'Iniciando execução: Rotina Teste Scheduler (ID: 29)', NULL, NULL, NULL, '2026-02-03 15:29:36.189036');
INSERT INTO public.logs_sistema VALUES (626, 'info', 'scheduler', 'Rotina Teste Scheduler concluído em 0.14s - Status: falha', NULL, NULL, NULL, '2026-02-03 15:29:36.33879');
INSERT INTO public.logs_sistema VALUES (627, 'debug', 'scheduler', 'Próxima execução da rotina Rotina Teste Scheduler: 2026-02-03 15:30:00', NULL, NULL, NULL, '2026-02-03 15:29:36.344989');
INSERT INTO public.logs_sistema VALUES (628, 'debug', 'scheduler', 'Verificando rotinas agendadas...', NULL, NULL, NULL, '2026-02-03 15:30:36.382881');
INSERT INTO public.logs_sistema VALUES (629, 'info', 'scheduler', '1 rotina(s) para executar', NULL, NULL, NULL, '2026-02-03 15:30:36.386978');
INSERT INTO public.logs_sistema VALUES (630, 'info', 'scheduler', 'Iniciando execução: Rotina Teste Scheduler (ID: 29)', NULL, NULL, NULL, '2026-02-03 15:30:36.388884');
INSERT INTO public.logs_sistema VALUES (631, 'info', 'scheduler', 'Rotina Teste Scheduler concluído em 0.05s - Status: falha', NULL, NULL, NULL, '2026-02-03 15:30:36.445128');
INSERT INTO public.logs_sistema VALUES (632, 'debug', 'scheduler', 'Próxima execução da rotina Rotina Teste Scheduler: 2026-02-03 15:31:00', NULL, NULL, NULL, '2026-02-03 15:30:36.457324');
INSERT INTO public.logs_sistema VALUES (633, 'debug', 'scheduler', 'Verificando rotinas agendadas...', NULL, NULL, NULL, '2026-02-03 15:31:36.538391');
INSERT INTO public.logs_sistema VALUES (634, 'info', 'scheduler', '1 rotina(s) para executar', NULL, NULL, NULL, '2026-02-03 15:31:36.573502');
INSERT INTO public.logs_sistema VALUES (635, 'info', 'scheduler', 'Iniciando execução: Rotina Teste Scheduler (ID: 29)', NULL, NULL, NULL, '2026-02-03 15:31:36.57806');
INSERT INTO public.logs_sistema VALUES (636, 'info', 'scheduler', 'Rotina Teste Scheduler concluído em 0.05s - Status: falha', NULL, NULL, NULL, '2026-02-03 15:31:36.636151');
INSERT INTO public.logs_sistema VALUES (637, 'debug', 'scheduler', 'Próxima execução da rotina Rotina Teste Scheduler: 2026-02-03 15:32:00', NULL, NULL, NULL, '2026-02-03 15:31:36.640155');
INSERT INTO public.logs_sistema VALUES (638, 'debug', 'scheduler', 'Verificando rotinas agendadas...', NULL, NULL, NULL, '2026-02-03 15:32:36.678557');
INSERT INTO public.logs_sistema VALUES (639, 'info', 'scheduler', '1 rotina(s) para executar', NULL, NULL, NULL, '2026-02-03 15:32:36.681382');
INSERT INTO public.logs_sistema VALUES (640, 'info', 'scheduler', 'Iniciando execução: Rotina Teste Scheduler (ID: 29)', NULL, NULL, NULL, '2026-02-03 15:32:36.683725');
INSERT INTO public.logs_sistema VALUES (641, 'info', 'scheduler', 'Rotina Teste Scheduler concluído em 0.06s - Status: falha', NULL, NULL, NULL, '2026-02-03 15:32:36.746492');
INSERT INTO public.logs_sistema VALUES (642, 'debug', 'scheduler', 'Próxima execução da rotina Rotina Teste Scheduler: 2026-02-03 15:33:00', NULL, NULL, NULL, '2026-02-03 15:32:36.752074');
INSERT INTO public.logs_sistema VALUES (643, 'debug', 'scheduler', 'Verificando rotinas agendadas...', NULL, NULL, NULL, '2026-02-03 15:33:36.807405');
INSERT INTO public.logs_sistema VALUES (644, 'info', 'scheduler', '1 rotina(s) para executar', NULL, NULL, NULL, '2026-02-03 15:33:36.825777');
INSERT INTO public.logs_sistema VALUES (645, 'info', 'scheduler', 'Iniciando execução: Rotina Teste Scheduler (ID: 29)', NULL, NULL, NULL, '2026-02-03 15:33:36.830127');
INSERT INTO public.logs_sistema VALUES (646, 'info', 'scheduler', 'Rotina Teste Scheduler concluído em 0.06s - Status: falha', NULL, NULL, NULL, '2026-02-03 15:33:36.891961');
INSERT INTO public.logs_sistema VALUES (647, 'debug', 'scheduler', 'Próxima execução da rotina Rotina Teste Scheduler: 2026-02-03 15:34:00', NULL, NULL, NULL, '2026-02-03 15:33:36.895128');
INSERT INTO public.logs_sistema VALUES (648, 'info', 'scheduler', 'Agendamento configurado para rotina ID : */2 * * * *', NULL, 1, NULL, '2026-02-03 15:35:06.900992');
INSERT INTO public.logs_sistema VALUES (649, 'info', 'scheduler', 'Rotina 25 ativada', NULL, 1, NULL, '2026-02-03 15:35:14.872057');
INSERT INTO public.logs_sistema VALUES (650, 'info', 'scheduler', 'Agendamento configurado para rotina ID : */15 * * * *', NULL, NULL, NULL, '2026-02-03 15:42:22.141621');
INSERT INTO public.logs_sistema VALUES (651, 'info', 'scheduler', 'Agendamento configurado para rotina ID 25: */15 * * * *', NULL, NULL, NULL, '2026-02-03 15:42:46.932889');
INSERT INTO public.logs_sistema VALUES (652, 'info', 'scheduler', 'Agendamento configurado para rotina ID 25: */5 * * * *', NULL, 1, NULL, '2026-02-03 15:44:11.487074');
INSERT INTO public.logs_sistema VALUES (653, 'info', 'scheduler', 'Rotina 25 ativada', NULL, 1, NULL, '2026-02-03 15:44:15.073325');
INSERT INTO public.logs_sistema VALUES (654, 'info', 'scheduler', 'Scheduler Worker iniciado (PID: 8356)', NULL, NULL, NULL, '2026-02-03 16:21:48.129243');
INSERT INTO public.logs_sistema VALUES (655, 'debug', 'scheduler', 'Verificando rotinas agendadas...', NULL, NULL, NULL, '2026-02-03 16:21:48.17407');
INSERT INTO public.logs_sistema VALUES (656, 'info', 'scheduler', '1 rotina(s) ativas para verificar', NULL, NULL, NULL, '2026-02-03 16:21:48.1844');
INSERT INTO public.logs_sistema VALUES (657, 'info', 'scheduler', 'Iniciando execução: DMC-Movie - rotina1 (ID: 25)', NULL, NULL, NULL, '2026-02-03 16:21:48.189381');
INSERT INTO public.logs_sistema VALUES (658, 'error', 'scheduler', 'Erro na verificação: SQLSTATE[22021]: Character not in repertoire: 7 ERRO:  sequência de bytes é inválida para codificação "UTF8": 0xe7 0xe3 0x6f
CONTEXT:  parâmetro de portal sem nome $3', NULL, NULL, NULL, '2026-02-03 16:21:48.282444');
INSERT INTO public.logs_sistema VALUES (659, 'info', 'scheduler', 'Agendamento configurado para rotina ID 25: */2 * * * *', NULL, 1, NULL, '2026-02-03 16:26:52.745647');
INSERT INTO public.logs_sistema VALUES (660, 'info', 'scheduler', 'Agendamento configurado para rotina ID 25: */1 * * * *', NULL, 1, NULL, '2026-02-03 16:45:25.908613');
INSERT INTO public.logs_sistema VALUES (661, 'info', 'scheduler', 'Agendamento removido da rotina ID 25', NULL, 1, NULL, '2026-02-04 16:27:26.804229');


--
-- TOC entry 5237 (class 0 OID 45529)
-- Dependencies: 224
-- Data for Name: schedules; Type: TABLE DATA; Schema: public; Owner: postgres
--



--
-- TOC entry 5257 (class 0 OID 45711)
-- Dependencies: 245
-- Data for Name: tb_api_externas; Type: TABLE DATA; Schema: public; Owner: postgres
--

INSERT INTO public.tb_api_externas VALUES (1, 'JSONPlaceholder - Posts', 'API de teste para verificar posts', 'https://jsonplaceholder.typicode.com/posts/1', 'GET', '{}', 'none', '{}', NULL, 'json', 30, 30, true, NULL, NULL, NULL, NULL, '2026-02-04 16:35:46.543643-03', '2026-02-04 16:35:46.543643-03');
INSERT INTO public.tb_api_externas VALUES (2, 'JSONPlaceholder - Users', 'API de teste para verificar usuários', 'https://jsonplaceholder.typicode.com/users', 'GET', '{}', 'none', '{}', NULL, 'json', 60, 30, true, NULL, NULL, NULL, NULL, '2026-02-04 16:35:46.543643-03', '2026-02-04 16:35:46.543643-03');
INSERT INTO public.tb_api_externas VALUES (3, 'JSONPlaceholder - Posts', 'API pública para testes', 'https://jsonplaceholder.typicode.com/posts', 'GET', '{}', 'none', '{}', NULL, 'json', 60, 30, true, NULL, NULL, NULL, NULL, '2026-02-04 17:00:25.562939-03', '2026-02-04 17:00:25.562939-03');
INSERT INTO public.tb_api_externas VALUES (4, 'JSONPlaceholder - Users', 'API de usuários para testes', 'https://jsonplaceholder.typicode.com/users', 'GET', '{}', 'none', '{}', NULL, 'json', 120, 30, true, NULL, NULL, NULL, NULL, '2026-02-04 17:00:25.566021-03', '2026-02-04 17:00:25.566021-03');


--
-- TOC entry 5231 (class 0 OID 45485)
-- Dependencies: 218
-- Data for Name: tb_arquivos; Type: TABLE DATA; Schema: public; Owner: postgres
--

INSERT INTO public.tb_arquivos VALUES (1, 'dt_b1_20260130T120315_04b20c4be64b40f19e6ef66216200c1b.csv', 66, '\x69643b6e616d650d0a313b610d0a323b620d0a333b630d0a343b630d0a353b630d0a363b630d0a373b630d0a383b630d0a393b630d0a31303b630d0a31313b630d0a', '2026-01-30 09:03:15.786811-03', 'dt', 'b1');
INSERT INTO public.tb_arquivos VALUES (2, 't1_b1_20260130T120322_c378c6092e074789a55086478283a334.csv', 66, '\x69643b6e616d650d0a313b610d0a323b620d0a333b630d0a343b630d0a353b630d0a363b630d0a373b630d0a383b630d0a393b630d0a31303b630d0a31313b630d0a', '2026-01-30 09:03:22.509801-03', 't1', 'b1');
INSERT INTO public.tb_arquivos VALUES (3, 'sch_b_20260130T120327_fe62995b8c6e4aa58a877b7dc5f4b85c.csv', 72, '\x69643b6e616d650d0a313b610d0a323b620d0a333b630d0a343b630d0a353b630d0a363b630d0a373b630d0a383b630d0a393b630d0a31303b630d0a31313b630d0a31323b630d0a', '2026-01-30 09:03:27.586165-03', 'sch', 'b');


--
-- TOC entry 5233 (class 0 OID 45495)
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
-- TOC entry 5245 (class 0 OID 45592)
-- Dependencies: 232
-- Data for Name: tb_blocos_rotina; Type: TABLE DATA; Schema: public; Owner: postgres
--

INSERT INTO public.tb_blocos_rotina OVERRIDING SYSTEM VALUE VALUES (41, 25, 'step_1', 1, 'SELECT id, codigo_bloco, ordem FROM tb_blocos_rotina LIMIT 5', 'SELECT', '2026-02-03 08:44:49.881989-03');


--
-- TOC entry 5259 (class 0 OID 45732)
-- Dependencies: 247
-- Data for Name: tb_eventos_api; Type: TABLE DATA; Schema: public; Owner: postgres
--

INSERT INTO public.tb_eventos_api VALUES (1, 3, 'Post ID Maior que 50', 'Dispara quando encontra post com ID > 50', '$[0].id', NULL, 'number', 'greater_than', '50', 'trigger_workflow', 1, true, true, NULL, NULL, NULL, 0, '2026-02-04 17:00:25.594742-03', '2026-02-04 17:00:25.594742-03');


--
-- TOC entry 5247 (class 0 OID 45607)
-- Dependencies: 234
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
INSERT INTO public.tb_logs_execucao OVERRIDING SYSTEM VALUE VALUES (19, 25, '2026-02-03 16:25:35.103084-03', '2026-02-03 16:25:37.107345-03', 'sucesso', NULL, NULL, NULL, 2000, 0, 0, 0, NULL, NULL, 10);
INSERT INTO public.tb_logs_execucao OVERRIDING SYSTEM VALUE VALUES (20, 25, '2026-02-03 16:25:51.354522-03', '2026-02-03 16:25:53.364296-03', 'sucesso', NULL, NULL, NULL, 2000, 0, 0, 0, NULL, NULL, 10);
INSERT INTO public.tb_logs_execucao OVERRIDING SYSTEM VALUE VALUES (18, 25, '2026-02-03 16:21:48.19039-03', '2026-02-03 16:37:22.654654-03', 'falha', 'Execucao travada - limpo automaticamente', NULL, NULL, NULL, 0, 0, 0, NULL, NULL, 0);
INSERT INTO public.tb_logs_execucao OVERRIDING SYSTEM VALUE VALUES (21, 25, '2026-02-03 16:38:04.675166-03', '2026-02-03 16:38:06.760194-03', 'sucesso', NULL, NULL, NULL, 2049, 0, 0, 0, NULL, NULL, 51);
INSERT INTO public.tb_logs_execucao OVERRIDING SYSTEM VALUE VALUES (22, 25, '2026-02-03 16:42:06.911793-03', '2026-02-03 16:42:08.920219-03', 'sucesso', NULL, NULL, NULL, 2006, 0, 0, 0, NULL, NULL, 38);
INSERT INTO public.tb_logs_execucao OVERRIDING SYSTEM VALUE VALUES (23, 25, '2026-02-03 16:44:09.008344-03', '2026-02-03 16:44:11.023045-03', 'sucesso', NULL, NULL, NULL, 2011, 0, 0, 0, NULL, NULL, 44);
INSERT INTO public.tb_logs_execucao OVERRIDING SYSTEM VALUE VALUES (24, 25, '2026-02-03 16:47:01.151509-03', '2026-02-03 16:47:03.165748-03', 'sucesso', NULL, NULL, NULL, 2010, 0, 0, 0, NULL, NULL, 47);
INSERT INTO public.tb_logs_execucao OVERRIDING SYSTEM VALUE VALUES (25, 25, '2026-02-03 16:48:03.257294-03', '2026-02-03 16:48:05.27732-03', 'sucesso', NULL, NULL, NULL, 2015, 0, 0, 0, NULL, NULL, 85);
INSERT INTO public.tb_logs_execucao OVERRIDING SYSTEM VALUE VALUES (26, 25, '2026-02-03 16:49:05.37411-03', '2026-02-03 16:49:07.393245-03', 'sucesso', NULL, NULL, NULL, 2015, 0, 0, 0, NULL, NULL, 47);
INSERT INTO public.tb_logs_execucao OVERRIDING SYSTEM VALUE VALUES (27, 25, '2026-02-03 16:59:52.6695-03', '2026-02-03 16:59:52.679778-03', 'falha', 'SQLSTATE[42P01]: Undefined table: 7 ERRO:  relação "tb_blocos" não existe
LINE 1: SELECT * FROM tb_blocos WHERE id_rotina = $1 ORDER BY ordem ...
                      ^', '[]', NULL, 5, 0, 0, 0, NULL, NULL, 0);
INSERT INTO public.tb_logs_execucao OVERRIDING SYSTEM VALUE VALUES (28, 25, '2026-02-03 17:00:02.707186-03', '2026-02-03 17:00:02.715668-03', 'falha', 'SQLSTATE[42P01]: Undefined table: 7 ERRO:  relação "tb_blocos" não existe
LINE 1: SELECT * FROM tb_blocos WHERE id_rotina = $1 ORDER BY ordem ...
                      ^', '[]', NULL, 4, 0, 0, 0, NULL, NULL, 0);
INSERT INTO public.tb_logs_execucao OVERRIDING SYSTEM VALUE VALUES (29, 25, '2026-02-03 17:00:24.657616-03', NULL, 'executando', NULL, NULL, NULL, NULL, 0, 0, 0, NULL, NULL, 0);
INSERT INTO public.tb_logs_execucao OVERRIDING SYSTEM VALUE VALUES (30, 25, '2026-02-03 17:04:28.785066-03', '2026-02-03 17:04:28.884568-03', 'sucesso', NULL, '[{"sql": "select * from public.users;", "erro": "SQLSTATE[42P01]: Undefined table: 7 ERRO:  relação \"public.users\" não existe\nLINE 1: select * from public.users;\n                      ^", "tipo": "SELECT", "bloco": "step_1", "ordem": 1, "status": "erro", "registros": 0, "resultado": null, "duracao_ms": 32}]', NULL, 82, 1, 0, 1, NULL, NULL, 0);
INSERT INTO public.tb_logs_execucao OVERRIDING SYSTEM VALUE VALUES (31, 25, '2026-02-03 17:05:08.972679-03', '2026-02-03 17:05:08.989835-03', 'sucesso', NULL, '[{"sql": "select * from public.users;", "erro": "SQLSTATE[42P01]: Undefined table: 7 ERRO:  relação \"public.users\" não existe\nLINE 1: select * from public.users;\n                      ^", "tipo": "SELECT", "bloco": "step_1", "ordem": 1, "status": "erro", "registros": 0, "resultado": null, "duracao_ms": 5}]', NULL, 13, 1, 0, 1, NULL, NULL, 0);
INSERT INTO public.tb_logs_execucao OVERRIDING SYSTEM VALUE VALUES (32, 25, '2026-02-03 17:05:33.65145-03', '2026-02-03 17:05:33.695163-03', 'sucesso', NULL, '[{"sql": "select * from public.users;", "erro": "SQLSTATE[42P01]: Undefined table: 7 ERRO:  relação \"public.users\" não existe\nLINE 1: select * from public.users;\n                      ^", "tipo": "SELECT", "bloco": "step_1", "ordem": 1, "status": "erro", "registros": 0, "resultado": null, "duracao_ms": 6}]', NULL, 30, 1, 0, 1, NULL, NULL, 0);
INSERT INTO public.tb_logs_execucao OVERRIDING SYSTEM VALUE VALUES (33, 25, '2026-02-03 17:07:21.96034-03', '2026-02-03 17:07:21.987304-03', 'sucesso', NULL, '[{"sql": "SELECT id, codigo_bloco, ordem FROM tb_blocos_rotina LIMIT 5", "erro": null, "tipo": "SELECT", "bloco": "step_1", "ordem": 1, "status": "sucesso", "registros": 1, "resultado": "Linhas afetadas: 1", "duracao_ms": 4}]', NULL, 20, 1, 1, 0, NULL, NULL, 1);
INSERT INTO public.tb_logs_execucao OVERRIDING SYSTEM VALUE VALUES (34, NULL, '2026-02-03 17:10:18.189387-03', '2026-02-03 17:10:18.189387-03', 'sucesso', NULL, '[{"sql": "select * from public.users;", "erro": null, "tipo": "SELECT", "bloco": "step_1", "ordem": 1, "status": "sucesso", "id_bloco": 42, "registros": 3, "resultado": "Arquivo gerado: C:\\xampp\\htdocs\\DMC-DATALOAD\\app\\Servicos/../../storage/logs\\execucao_30_step_1_1770149418.csv (Linhas: 3)", "duracao_ms": 10, "arquivo_csv": "C:\\xampp\\htdocs\\DMC-DATALOAD\\app\\Servicos/../../storage/logs\\execucao_30_step_1_1770149418.csv"}, {"sql": "", "erro": "PDO::query(): Argument #1 ($query) cannot be empty", "tipo": "SELECT", "bloco": "", "ordem": 2, "status": "falha", "id_bloco": 43, "registros": 0, "resultado": "", "duracao_ms": 0, "arquivo_csv": null}]', NULL, 72, 2, 1, 1, NULL, NULL, 3);


--
-- TOC entry 5249 (class 0 OID 45632)
-- Dependencies: 236
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
-- TOC entry 5251 (class 0 OID 45647)
-- Dependencies: 238
-- Data for Name: tb_metricas_sistema; Type: TABLE DATA; Schema: public; Owner: postgres
--



--
-- TOC entry 5241 (class 0 OID 45561)
-- Dependencies: 228
-- Data for Name: tb_perfis_conexao; Type: TABLE DATA; Schema: public; Owner: postgres
--

INSERT INTO public.tb_perfis_conexao OVERRIDING SYSTEM VALUE VALUES (11, 'DMC-Movie', 'postgres', 'localhost', 5433, 'db_dmc_movie', 'postgres', 'IaQOIpqPMZUIO/sYzN5eUA==:63SV0/7w0KANojIiL/xbrA==', '{}', '2026-02-03 08:42:25.170637-03');
INSERT INTO public.tb_perfis_conexao OVERRIDING SYSTEM VALUE VALUES (12, 'HOMOLOGAÇÃO - C_ERGON', 'oracle', '10.238.205.116', 1521, NULL, 'c_ergon', 'dxRalNP4R4y2P4ZWoaEoWA==:cnaJRHeklyu3HewGZG7WFA==', '{"sid": "SADRHPRO", "tipo_conexao_oracle": "sid"}', '2026-02-03 11:00:51.717945-03');
INSERT INTO public.tb_perfis_conexao OVERRIDING SYSTEM VALUE VALUES (14, 'HOMOLOGAÇÃO - DMC-Exam', 'mysql', 'localhost', 3306, 'db_dmc_exames', 'root', 'A+JafKH66NkwecA9JeJStQ==:lp5sPKxpjalZkV2vn1iGEg==', '[]', '2026-02-03 11:08:55.41701-03');


--
-- TOC entry 5275 (class 0 OID 46278)
-- Dependencies: 263
-- Data for Name: tb_pipeline_execucoes; Type: TABLE DATA; Schema: public; Owner: postgres
--



--
-- TOC entry 5273 (class 0 OID 46258)
-- Dependencies: 261
-- Data for Name: tb_pipelines; Type: TABLE DATA; Schema: public; Owner: postgres
--



--
-- TOC entry 5243 (class 0 OID 45572)
-- Dependencies: 230
-- Data for Name: tb_rotinas; Type: TABLE DATA; Schema: public; Owner: postgres
--

INSERT INTO public.tb_rotinas OVERRIDING SYSTEM VALUE VALUES (25, 'DMC-Movie - rotina1', 'Consultas de rotina', 11, 1, false, NULL, NULL, NULL, '2026-02-03 08:44:15.712766-03', NULL, false, '2026-02-03 17:08:00-03', 0, 3, '2026-02-03 17:07:21.998825-03', NULL, NULL, NULL, false, 300, true);


--
-- TOC entry 5239 (class 0 OID 45550)
-- Dependencies: 226
-- Data for Name: tb_usuarios; Type: TABLE DATA; Schema: public; Owner: postgres
--

INSERT INTO public.tb_usuarios OVERRIDING SYSTEM VALUE VALUES (1, 'admin', '$2y$10$LikSAYU.brSi1ILxdi8LyuTkScnB.bz6we1gxc68fo40thOErlkY.', false, 'admin', '2026-02-02 11:08:48.320843-03', false);
INSERT INTO public.tb_usuarios OVERRIDING SYSTEM VALUE VALUES (3, 'leo', '$2y$10$uGHQgUSbLALxsrYrydsNEuOS/aXwyyKU6h.Cq2/jMIzBa0gzPdX9i', false, 'user', '2026-02-03 09:27:27.440711-03', false);


--
-- TOC entry 5261 (class 0 OID 45756)
-- Dependencies: 249
-- Data for Name: tb_valores_capturados; Type: TABLE DATA; Schema: public; Owner: postgres
--



--
-- TOC entry 5253 (class 0 OID 45658)
-- Dependencies: 240
-- Data for Name: tb_worker_heartbeat; Type: TABLE DATA; Schema: public; Owner: postgres
--



--
-- TOC entry 5267 (class 0 OID 45822)
-- Dependencies: 255
-- Data for Name: tb_workflow_edges; Type: TABLE DATA; Schema: public; Owner: postgres
--



--
-- TOC entry 5269 (class 0 OID 45844)
-- Dependencies: 257
-- Data for Name: tb_workflow_execucoes; Type: TABLE DATA; Schema: public; Owner: postgres
--



--
-- TOC entry 5271 (class 0 OID 45872)
-- Dependencies: 259
-- Data for Name: tb_workflow_node_execucoes; Type: TABLE DATA; Schema: public; Owner: postgres
--



--
-- TOC entry 5265 (class 0 OID 45799)
-- Dependencies: 253
-- Data for Name: tb_workflow_nodes; Type: TABLE DATA; Schema: public; Owner: postgres
--



--
-- TOC entry 5263 (class 0 OID 45781)
-- Dependencies: 251
-- Data for Name: tb_workflows; Type: TABLE DATA; Schema: public; Owner: postgres
--

INSERT INTO public.tb_workflows VALUES (1, 'Workflow de Teste', 'Workflow criado automaticamente para testes', true, '{"edges": [{"id": "edge_1", "source": "node_1", "target": "node_2", "condicao": "always"}, {"id": "edge_2", "source": "node_2", "target": "node_3", "condicao": "always"}], "nodes": [{"id": "node_1", "tipo": "trigger", "label": "Início", "posicao": {"x": 100, "y": 50}, "configuracao": []}, {"id": "node_2", "tipo": "notification", "label": "Notificar", "posicao": {"x": 100, "y": 200}, "configuracao": {"tipo": "log", "mensagem": "Workflow executado!"}}, {"id": "node_3", "tipo": "end", "label": "Fim", "posicao": {"x": 100, "y": 350}, "configuracao": {"status": "success"}}]}', 1, 'api_event', '{}', NULL, '2026-02-04 17:00:25.569394-03', '2026-02-04 17:00:25.569394-03');


--
-- TOC entry 5314 (class 0 OID 0)
-- Dependencies: 221
-- Name: connections_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.connections_id_seq', 1, true);


--
-- TOC entry 5315 (class 0 OID 0)
-- Dependencies: 242
-- Name: logs_sistema_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.logs_sistema_id_seq', 661, true);


--
-- TOC entry 5316 (class 0 OID 0)
-- Dependencies: 223
-- Name: schedules_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.schedules_id_seq', 1, true);


--
-- TOC entry 5317 (class 0 OID 0)
-- Dependencies: 244
-- Name: tb_api_externas_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.tb_api_externas_id_seq', 4, true);


--
-- TOC entry 5318 (class 0 OID 0)
-- Dependencies: 217
-- Name: tb_arquivos_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.tb_arquivos_id_seq', 3, true);


--
-- TOC entry 5319 (class 0 OID 0)
-- Dependencies: 219
-- Name: tb_auditoria_rotina_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.tb_auditoria_rotina_id_seq', 5, true);


--
-- TOC entry 5320 (class 0 OID 0)
-- Dependencies: 231
-- Name: tb_blocos_rotina_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.tb_blocos_rotina_id_seq', 43, true);


--
-- TOC entry 5321 (class 0 OID 0)
-- Dependencies: 246
-- Name: tb_eventos_api_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.tb_eventos_api_id_seq', 1, true);


--
-- TOC entry 5322 (class 0 OID 0)
-- Dependencies: 233
-- Name: tb_logs_execucao_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.tb_logs_execucao_id_seq', 34, true);


--
-- TOC entry 5323 (class 0 OID 0)
-- Dependencies: 235
-- Name: tb_logs_sistema_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.tb_logs_sistema_id_seq', 10, true);


--
-- TOC entry 5324 (class 0 OID 0)
-- Dependencies: 237
-- Name: tb_metricas_sistema_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.tb_metricas_sistema_id_seq', 1, false);


--
-- TOC entry 5325 (class 0 OID 0)
-- Dependencies: 227
-- Name: tb_perfis_conexao_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.tb_perfis_conexao_id_seq', 14, true);


--
-- TOC entry 5326 (class 0 OID 0)
-- Dependencies: 262
-- Name: tb_pipeline_execucoes_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.tb_pipeline_execucoes_id_seq', 1, false);


--
-- TOC entry 5327 (class 0 OID 0)
-- Dependencies: 260
-- Name: tb_pipelines_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.tb_pipelines_id_seq', 1, false);


--
-- TOC entry 5328 (class 0 OID 0)
-- Dependencies: 229
-- Name: tb_rotinas_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.tb_rotinas_id_seq', 30, true);


--
-- TOC entry 5329 (class 0 OID 0)
-- Dependencies: 225
-- Name: tb_usuarios_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.tb_usuarios_id_seq', 3, true);


--
-- TOC entry 5330 (class 0 OID 0)
-- Dependencies: 248
-- Name: tb_valores_capturados_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.tb_valores_capturados_id_seq', 1, false);


--
-- TOC entry 5331 (class 0 OID 0)
-- Dependencies: 239
-- Name: tb_worker_heartbeat_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.tb_worker_heartbeat_id_seq', 1, false);


--
-- TOC entry 5332 (class 0 OID 0)
-- Dependencies: 254
-- Name: tb_workflow_edges_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.tb_workflow_edges_id_seq', 1, false);


--
-- TOC entry 5333 (class 0 OID 0)
-- Dependencies: 256
-- Name: tb_workflow_execucoes_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.tb_workflow_execucoes_id_seq', 1, false);


--
-- TOC entry 5334 (class 0 OID 0)
-- Dependencies: 258
-- Name: tb_workflow_node_execucoes_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.tb_workflow_node_execucoes_id_seq', 1, false);


--
-- TOC entry 5335 (class 0 OID 0)
-- Dependencies: 252
-- Name: tb_workflow_nodes_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.tb_workflow_nodes_id_seq', 1, false);


--
-- TOC entry 5336 (class 0 OID 0)
-- Dependencies: 250
-- Name: tb_workflows_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.tb_workflows_id_seq', 1, true);


--
-- TOC entry 4984 (class 2606 OID 45527)
-- Name: connections connections_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.connections
    ADD CONSTRAINT connections_pkey PRIMARY KEY (id);


--
-- TOC entry 5021 (class 2606 OID 45686)
-- Name: logs_sistema logs_sistema_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.logs_sistema
    ADD CONSTRAINT logs_sistema_pkey PRIMARY KEY (id);


--
-- TOC entry 4986 (class 2606 OID 45536)
-- Name: schedules schedules_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.schedules
    ADD CONSTRAINT schedules_pkey PRIMARY KEY (id);


--
-- TOC entry 5025 (class 2606 OID 45728)
-- Name: tb_api_externas tb_api_externas_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tb_api_externas
    ADD CONSTRAINT tb_api_externas_pkey PRIMARY KEY (id);


--
-- TOC entry 4980 (class 2606 OID 45493)
-- Name: tb_arquivos tb_arquivos_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tb_arquivos
    ADD CONSTRAINT tb_arquivos_pkey PRIMARY KEY (id);


--
-- TOC entry 4982 (class 2606 OID 45502)
-- Name: tb_auditoria_rotina tb_auditoria_rotina_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tb_auditoria_rotina
    ADD CONSTRAINT tb_auditoria_rotina_pkey PRIMARY KEY (id);


--
-- TOC entry 4999 (class 2606 OID 45600)
-- Name: tb_blocos_rotina tb_blocos_rotina_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tb_blocos_rotina
    ADD CONSTRAINT tb_blocos_rotina_pkey PRIMARY KEY (id);


--
-- TOC entry 5030 (class 2606 OID 45746)
-- Name: tb_eventos_api tb_eventos_api_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tb_eventos_api
    ADD CONSTRAINT tb_eventos_api_pkey PRIMARY KEY (id);


--
-- TOC entry 5004 (class 2606 OID 45614)
-- Name: tb_logs_execucao tb_logs_execucao_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tb_logs_execucao
    ADD CONSTRAINT tb_logs_execucao_pkey PRIMARY KEY (id);


--
-- TOC entry 5009 (class 2606 OID 45642)
-- Name: tb_logs_sistema tb_logs_sistema_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tb_logs_sistema
    ADD CONSTRAINT tb_logs_sistema_pkey PRIMARY KEY (id);


--
-- TOC entry 5011 (class 2606 OID 45656)
-- Name: tb_metricas_sistema tb_metricas_sistema_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tb_metricas_sistema
    ADD CONSTRAINT tb_metricas_sistema_pkey PRIMARY KEY (id);


--
-- TOC entry 4990 (class 2606 OID 45568)
-- Name: tb_perfis_conexao tb_perfis_conexao_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tb_perfis_conexao
    ADD CONSTRAINT tb_perfis_conexao_pkey PRIMARY KEY (id);


--
-- TOC entry 5069 (class 2606 OID 46293)
-- Name: tb_pipeline_execucoes tb_pipeline_execucoes_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tb_pipeline_execucoes
    ADD CONSTRAINT tb_pipeline_execucoes_pkey PRIMARY KEY (id);


--
-- TOC entry 5065 (class 2606 OID 46276)
-- Name: tb_pipelines tb_pipelines_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tb_pipelines
    ADD CONSTRAINT tb_pipelines_pkey PRIMARY KEY (id);


--
-- TOC entry 4997 (class 2606 OID 45580)
-- Name: tb_rotinas tb_rotinas_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tb_rotinas
    ADD CONSTRAINT tb_rotinas_pkey PRIMARY KEY (id);


--
-- TOC entry 4988 (class 2606 OID 45559)
-- Name: tb_usuarios tb_usuarios_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tb_usuarios
    ADD CONSTRAINT tb_usuarios_pkey PRIMARY KEY (id);


--
-- TOC entry 5035 (class 2606 OID 45766)
-- Name: tb_valores_capturados tb_valores_capturados_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tb_valores_capturados
    ADD CONSTRAINT tb_valores_capturados_pkey PRIMARY KEY (id);


--
-- TOC entry 5015 (class 2606 OID 45667)
-- Name: tb_worker_heartbeat tb_worker_heartbeat_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tb_worker_heartbeat
    ADD CONSTRAINT tb_worker_heartbeat_pkey PRIMARY KEY (id);


--
-- TOC entry 5017 (class 2606 OID 45669)
-- Name: tb_worker_heartbeat tb_worker_heartbeat_worker_id_key; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tb_worker_heartbeat
    ADD CONSTRAINT tb_worker_heartbeat_worker_id_key UNIQUE (worker_id);


--
-- TOC entry 5050 (class 2606 OID 45834)
-- Name: tb_workflow_edges tb_workflow_edges_id_workflow_edge_id_key; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tb_workflow_edges
    ADD CONSTRAINT tb_workflow_edges_id_workflow_edge_id_key UNIQUE (id_workflow, edge_id);


--
-- TOC entry 5052 (class 2606 OID 45832)
-- Name: tb_workflow_edges tb_workflow_edges_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tb_workflow_edges
    ADD CONSTRAINT tb_workflow_edges_pkey PRIMARY KEY (id);


--
-- TOC entry 5057 (class 2606 OID 45862)
-- Name: tb_workflow_execucoes tb_workflow_execucoes_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tb_workflow_execucoes
    ADD CONSTRAINT tb_workflow_execucoes_pkey PRIMARY KEY (id);


--
-- TOC entry 5062 (class 2606 OID 45884)
-- Name: tb_workflow_node_execucoes tb_workflow_node_execucoes_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tb_workflow_node_execucoes
    ADD CONSTRAINT tb_workflow_node_execucoes_pkey PRIMARY KEY (id);


--
-- TOC entry 5043 (class 2606 OID 45813)
-- Name: tb_workflow_nodes tb_workflow_nodes_id_workflow_node_id_key; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tb_workflow_nodes
    ADD CONSTRAINT tb_workflow_nodes_id_workflow_node_id_key UNIQUE (id_workflow, node_id);


--
-- TOC entry 5045 (class 2606 OID 45811)
-- Name: tb_workflow_nodes tb_workflow_nodes_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tb_workflow_nodes
    ADD CONSTRAINT tb_workflow_nodes_pkey PRIMARY KEY (id);


--
-- TOC entry 5039 (class 2606 OID 45795)
-- Name: tb_workflows tb_workflows_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tb_workflows
    ADD CONSTRAINT tb_workflows_pkey PRIMARY KEY (id);


--
-- TOC entry 4992 (class 2606 OID 45570)
-- Name: tb_perfis_conexao uq_tb_perfis_conexao_nome; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tb_perfis_conexao
    ADD CONSTRAINT uq_tb_perfis_conexao_nome UNIQUE (nome_conexao);


--
-- TOC entry 5022 (class 1259 OID 45729)
-- Name: idx_api_externas_ativo; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_api_externas_ativo ON public.tb_api_externas USING btree (ativo);


--
-- TOC entry 5023 (class 1259 OID 45730)
-- Name: idx_api_externas_nome; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_api_externas_nome ON public.tb_api_externas USING btree (nome);


--
-- TOC entry 5026 (class 1259 OID 45753)
-- Name: idx_eventos_api_ativo; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_eventos_api_ativo ON public.tb_eventos_api USING btree (ativo);


--
-- TOC entry 5027 (class 1259 OID 45752)
-- Name: idx_eventos_api_id_api; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_eventos_api_id_api ON public.tb_eventos_api USING btree (id_api);


--
-- TOC entry 5028 (class 1259 OID 45754)
-- Name: idx_eventos_api_workflow; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_eventos_api_workflow ON public.tb_eventos_api USING btree (id_workflow);


--
-- TOC entry 5012 (class 1259 OID 45670)
-- Name: idx_heartbeat_status; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_heartbeat_status ON public.tb_worker_heartbeat USING btree (status);


--
-- TOC entry 5013 (class 1259 OID 45671)
-- Name: idx_heartbeat_ultimo; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_heartbeat_ultimo ON public.tb_worker_heartbeat USING btree (ultimo_heartbeat DESC);


--
-- TOC entry 5005 (class 1259 OID 45645)
-- Name: idx_logs_canal; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_logs_canal ON public.tb_logs_sistema USING btree (canal);


--
-- TOC entry 5018 (class 1259 OID 45692)
-- Name: idx_logs_categoria; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_logs_categoria ON public.logs_sistema USING btree (categoria);


--
-- TOC entry 5019 (class 1259 OID 45693)
-- Name: idx_logs_created_at; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_logs_created_at ON public.logs_sistema USING btree (created_at);


--
-- TOC entry 5006 (class 1259 OID 45644)
-- Name: idx_logs_criado_em; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_logs_criado_em ON public.tb_logs_sistema USING btree (criado_em DESC);


--
-- TOC entry 5000 (class 1259 OID 45629)
-- Name: idx_logs_data_inicio; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_logs_data_inicio ON public.tb_logs_execucao USING btree (data_inicio DESC);


--
-- TOC entry 5007 (class 1259 OID 45643)
-- Name: idx_logs_nivel; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_logs_nivel ON public.tb_logs_sistema USING btree (nivel);


--
-- TOC entry 5001 (class 1259 OID 45630)
-- Name: idx_logs_rotina_data; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_logs_rotina_data ON public.tb_logs_execucao USING btree (id_rotina, data_inicio DESC);


--
-- TOC entry 5002 (class 1259 OID 45628)
-- Name: idx_logs_status; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_logs_status ON public.tb_logs_execucao USING btree (status);


--
-- TOC entry 5058 (class 1259 OID 45892)
-- Name: idx_node_exec_node_id; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_node_exec_node_id ON public.tb_workflow_node_execucoes USING btree (node_id);


--
-- TOC entry 5059 (class 1259 OID 45891)
-- Name: idx_node_exec_status; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_node_exec_status ON public.tb_workflow_node_execucoes USING btree (status);


--
-- TOC entry 5060 (class 1259 OID 45890)
-- Name: idx_node_exec_workflow_exec; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_node_exec_workflow_exec ON public.tb_workflow_node_execucoes USING btree (id_workflow_execucao);


--
-- TOC entry 5066 (class 1259 OID 46299)
-- Name: idx_pipe_exec_pipeline; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_pipe_exec_pipeline ON public.tb_pipeline_execucoes USING btree (id_pipeline);


--
-- TOC entry 5067 (class 1259 OID 46300)
-- Name: idx_pipe_exec_status; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_pipe_exec_status ON public.tb_pipeline_execucoes USING btree (status);


--
-- TOC entry 5063 (class 1259 OID 46301)
-- Name: idx_pipelines_ativo; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_pipelines_ativo ON public.tb_pipelines USING btree (ativo);


--
-- TOC entry 4993 (class 1259 OID 45627)
-- Name: idx_rotinas_ativa_proxima; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_rotinas_ativa_proxima ON public.tb_rotinas USING btree (ativa, proxima_execucao) WHERE (ativa = true);


--
-- TOC entry 4994 (class 1259 OID 45709)
-- Name: idx_rotinas_datas_ignorar; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_rotinas_datas_ignorar ON public.tb_rotinas USING gin (datas_ignorar_json);


--
-- TOC entry 4995 (class 1259 OID 45708)
-- Name: idx_rotinas_periodo_agendamento; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_rotinas_periodo_agendamento ON public.tb_rotinas USING btree (data_inicio, data_fim) WHERE (agendamento_cron IS NOT NULL);


--
-- TOC entry 5031 (class 1259 OID 45779)
-- Name: idx_valores_capturados_data; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_valores_capturados_data ON public.tb_valores_capturados USING btree (data_captura);


--
-- TOC entry 5032 (class 1259 OID 45777)
-- Name: idx_valores_capturados_evento; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_valores_capturados_evento ON public.tb_valores_capturados USING btree (id_evento);


--
-- TOC entry 5033 (class 1259 OID 45778)
-- Name: idx_valores_capturados_processado; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_valores_capturados_processado ON public.tb_valores_capturados USING btree (processado);


--
-- TOC entry 5046 (class 1259 OID 45842)
-- Name: idx_workflow_edges_destino; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_workflow_edges_destino ON public.tb_workflow_edges USING btree (node_destino);


--
-- TOC entry 5047 (class 1259 OID 45841)
-- Name: idx_workflow_edges_origem; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_workflow_edges_origem ON public.tb_workflow_edges USING btree (node_origem);


--
-- TOC entry 5048 (class 1259 OID 45840)
-- Name: idx_workflow_edges_workflow; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_workflow_edges_workflow ON public.tb_workflow_edges USING btree (id_workflow);


--
-- TOC entry 5053 (class 1259 OID 45870)
-- Name: idx_workflow_exec_data; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_workflow_exec_data ON public.tb_workflow_execucoes USING btree (data_inicio);


--
-- TOC entry 5054 (class 1259 OID 45869)
-- Name: idx_workflow_exec_status; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_workflow_exec_status ON public.tb_workflow_execucoes USING btree (status);


--
-- TOC entry 5055 (class 1259 OID 45868)
-- Name: idx_workflow_exec_workflow; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_workflow_exec_workflow ON public.tb_workflow_execucoes USING btree (id_workflow);


--
-- TOC entry 5040 (class 1259 OID 45820)
-- Name: idx_workflow_nodes_tipo; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_workflow_nodes_tipo ON public.tb_workflow_nodes USING btree (tipo_node);


--
-- TOC entry 5041 (class 1259 OID 45819)
-- Name: idx_workflow_nodes_workflow; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_workflow_nodes_workflow ON public.tb_workflow_nodes USING btree (id_workflow);


--
-- TOC entry 5036 (class 1259 OID 45796)
-- Name: idx_workflows_ativo; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_workflows_ativo ON public.tb_workflows USING btree (ativo);


--
-- TOC entry 5037 (class 1259 OID 45797)
-- Name: idx_workflows_trigger; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_workflows_trigger ON public.tb_workflows USING btree (trigger_tipo);


--
-- TOC entry 5075 (class 2606 OID 45687)
-- Name: logs_sistema fk_logs_usuario; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.logs_sistema
    ADD CONSTRAINT fk_logs_usuario FOREIGN KEY (usuario_id) REFERENCES public.tb_usuarios(id) ON DELETE SET NULL;


--
-- TOC entry 5070 (class 2606 OID 45503)
-- Name: tb_auditoria_rotina tb_auditoria_rotina_id_arquivo_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tb_auditoria_rotina
    ADD CONSTRAINT tb_auditoria_rotina_id_arquivo_fkey FOREIGN KEY (id_arquivo) REFERENCES public.tb_arquivos(id);


--
-- TOC entry 5073 (class 2606 OID 45601)
-- Name: tb_blocos_rotina tb_blocos_rotina_id_rotina_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tb_blocos_rotina
    ADD CONSTRAINT tb_blocos_rotina_id_rotina_fkey FOREIGN KEY (id_rotina) REFERENCES public.tb_rotinas(id) ON DELETE CASCADE;


--
-- TOC entry 5076 (class 2606 OID 45747)
-- Name: tb_eventos_api tb_eventos_api_id_api_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tb_eventos_api
    ADD CONSTRAINT tb_eventos_api_id_api_fkey FOREIGN KEY (id_api) REFERENCES public.tb_api_externas(id) ON DELETE CASCADE;


--
-- TOC entry 5074 (class 2606 OID 45615)
-- Name: tb_logs_execucao tb_logs_execucao_id_rotina_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tb_logs_execucao
    ADD CONSTRAINT tb_logs_execucao_id_rotina_fkey FOREIGN KEY (id_rotina) REFERENCES public.tb_rotinas(id) ON DELETE SET NULL;


--
-- TOC entry 5083 (class 2606 OID 46294)
-- Name: tb_pipeline_execucoes tb_pipeline_execucoes_id_pipeline_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tb_pipeline_execucoes
    ADD CONSTRAINT tb_pipeline_execucoes_id_pipeline_fkey FOREIGN KEY (id_pipeline) REFERENCES public.tb_pipelines(id) ON DELETE CASCADE;


--
-- TOC entry 5071 (class 2606 OID 45581)
-- Name: tb_rotinas tb_rotinas_id_conexao_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tb_rotinas
    ADD CONSTRAINT tb_rotinas_id_conexao_fkey FOREIGN KEY (id_conexao) REFERENCES public.tb_perfis_conexao(id) ON DELETE RESTRICT;


--
-- TOC entry 5072 (class 2606 OID 45586)
-- Name: tb_rotinas tb_rotinas_id_usuario_criador_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tb_rotinas
    ADD CONSTRAINT tb_rotinas_id_usuario_criador_fkey FOREIGN KEY (id_usuario_criador) REFERENCES public.tb_usuarios(id) ON DELETE SET NULL;


--
-- TOC entry 5077 (class 2606 OID 45772)
-- Name: tb_valores_capturados tb_valores_capturados_id_api_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tb_valores_capturados
    ADD CONSTRAINT tb_valores_capturados_id_api_fkey FOREIGN KEY (id_api) REFERENCES public.tb_api_externas(id) ON DELETE CASCADE;


--
-- TOC entry 5078 (class 2606 OID 45767)
-- Name: tb_valores_capturados tb_valores_capturados_id_evento_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tb_valores_capturados
    ADD CONSTRAINT tb_valores_capturados_id_evento_fkey FOREIGN KEY (id_evento) REFERENCES public.tb_eventos_api(id) ON DELETE CASCADE;


--
-- TOC entry 5080 (class 2606 OID 45835)
-- Name: tb_workflow_edges tb_workflow_edges_id_workflow_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tb_workflow_edges
    ADD CONSTRAINT tb_workflow_edges_id_workflow_fkey FOREIGN KEY (id_workflow) REFERENCES public.tb_workflows(id) ON DELETE CASCADE;


--
-- TOC entry 5081 (class 2606 OID 45863)
-- Name: tb_workflow_execucoes tb_workflow_execucoes_id_workflow_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tb_workflow_execucoes
    ADD CONSTRAINT tb_workflow_execucoes_id_workflow_fkey FOREIGN KEY (id_workflow) REFERENCES public.tb_workflows(id) ON DELETE CASCADE;


--
-- TOC entry 5082 (class 2606 OID 45885)
-- Name: tb_workflow_node_execucoes tb_workflow_node_execucoes_id_workflow_execucao_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tb_workflow_node_execucoes
    ADD CONSTRAINT tb_workflow_node_execucoes_id_workflow_execucao_fkey FOREIGN KEY (id_workflow_execucao) REFERENCES public.tb_workflow_execucoes(id) ON DELETE CASCADE;


--
-- TOC entry 5079 (class 2606 OID 45814)
-- Name: tb_workflow_nodes tb_workflow_nodes_id_workflow_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tb_workflow_nodes
    ADD CONSTRAINT tb_workflow_nodes_id_workflow_fkey FOREIGN KEY (id_workflow) REFERENCES public.tb_workflows(id) ON DELETE CASCADE;


-- Completed on 2026-03-16 17:16:13

--
-- PostgreSQL database dump complete
--

