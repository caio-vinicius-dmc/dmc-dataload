--
-- PostgreSQL database dump
--

-- Dumped from database version 17.5
-- Dumped by pg_dump version 17.5

-- Started on 2026-03-27 16:58:28

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
-- TOC entry 5572 (class 0 OID 0)
-- Dependencies: 4
-- Name: SCHEMA public; Type: COMMENT; Schema: -; Owner: pg_database_owner
--

COMMENT ON SCHEMA public IS 'standard public schema';


--
-- TOC entry 937 (class 1247 OID 45538)
-- Name: tb_tipo_banco; Type: TYPE; Schema: public; Owner: postgres
--

CREATE TYPE public.tb_tipo_banco AS ENUM (
    'postgres',
    'mysql',
    'oracle',
    'sqlserver',
    'odbc',
    'sqlite',
    'mariadb'
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
-- TOC entry 5573 (class 0 OID 0)
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
-- TOC entry 5574 (class 0 OID 0)
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
    interval_seconds integer,
    criado_por integer
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
-- TOC entry 5575 (class 0 OID 0)
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
-- TOC entry 5576 (class 0 OID 0)
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
-- TOC entry 5577 (class 0 OID 0)
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
-- TOC entry 5578 (class 0 OID 0)
-- Dependencies: 217
-- Name: tb_arquivos_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.tb_arquivos_id_seq OWNED BY public.tb_arquivos.id;


--
-- TOC entry 283 (class 1259 OID 46508)
-- Name: tb_auditoria; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.tb_auditoria (
    id integer NOT NULL,
    acao character varying(50) NOT NULL,
    entidade character varying(50) NOT NULL,
    entidade_id integer,
    entidade_nome character varying(255),
    id_usuario integer NOT NULL,
    nome_usuario character varying(255),
    nivel_acesso character varying(30),
    dados_anteriores jsonb DEFAULT '{}'::jsonb,
    dados_novos jsonb DEFAULT '{}'::jsonb,
    ip_address character varying(45),
    user_agent text,
    criado_em timestamp with time zone DEFAULT CURRENT_TIMESTAMP
);


ALTER TABLE public.tb_auditoria OWNER TO postgres;

--
-- TOC entry 282 (class 1259 OID 46507)
-- Name: tb_auditoria_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.tb_auditoria_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.tb_auditoria_id_seq OWNER TO postgres;

--
-- TOC entry 5579 (class 0 OID 0)
-- Dependencies: 282
-- Name: tb_auditoria_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.tb_auditoria_id_seq OWNED BY public.tb_auditoria.id;


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
-- TOC entry 5580 (class 0 OID 0)
-- Dependencies: 219
-- Name: tb_auditoria_rotina_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.tb_auditoria_rotina_id_seq OWNED BY public.tb_auditoria_rotina.id;


--
-- TOC entry 292 (class 1259 OID 46588)
-- Name: tb_backups; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.tb_backups (
    id integer NOT NULL,
    nome character varying(255) NOT NULL,
    tipo character varying(30) DEFAULT 'completo'::character varying NOT NULL,
    tamanho_bytes bigint,
    caminho_arquivo text,
    checksum character varying(64),
    status character varying(20) DEFAULT 'pendente'::character varying NOT NULL,
    erro text,
    id_usuario integer,
    nome_usuario character varying(255),
    criado_em timestamp with time zone DEFAULT CURRENT_TIMESTAMP,
    concluido_em timestamp with time zone
);


ALTER TABLE public.tb_backups OWNER TO postgres;

--
-- TOC entry 291 (class 1259 OID 46587)
-- Name: tb_backups_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.tb_backups_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.tb_backups_id_seq OWNER TO postgres;

--
-- TOC entry 5581 (class 0 OID 0)
-- Dependencies: 291
-- Name: tb_backups_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.tb_backups_id_seq OWNED BY public.tb_backups.id;


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
-- TOC entry 290 (class 1259 OID 46570)
-- Name: tb_canais_notificacao; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.tb_canais_notificacao (
    id integer NOT NULL,
    nome character varying(100) NOT NULL,
    tipo character varying(20) NOT NULL,
    webhook_url text NOT NULL,
    canal character varying(100),
    ativo boolean DEFAULT true NOT NULL,
    notificar_sucesso boolean DEFAULT false NOT NULL,
    notificar_falha boolean DEFAULT true NOT NULL,
    notificar_inicio boolean DEFAULT false NOT NULL,
    tipos_recurso text[] DEFAULT ARRAY['rotina'::text, 'pipeline'::text, 'workflow'::text],
    criado_em timestamp with time zone DEFAULT CURRENT_TIMESTAMP,
    atualizado_em timestamp with time zone DEFAULT CURRENT_TIMESTAMP,
    criado_por integer
);


ALTER TABLE public.tb_canais_notificacao OWNER TO postgres;

--
-- TOC entry 289 (class 1259 OID 46569)
-- Name: tb_canais_notificacao_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.tb_canais_notificacao_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.tb_canais_notificacao_id_seq OWNER TO postgres;

--
-- TOC entry 5582 (class 0 OID 0)
-- Dependencies: 289
-- Name: tb_canais_notificacao_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.tb_canais_notificacao_id_seq OWNED BY public.tb_canais_notificacao.id;


--
-- TOC entry 281 (class 1259 OID 46469)
-- Name: tb_compartilhamentos; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.tb_compartilhamentos (
    id integer NOT NULL,
    tipo_recurso character varying(50) NOT NULL,
    id_recurso integer,
    id_usuario_dono integer NOT NULL,
    id_usuario_destino integer NOT NULL,
    permissao character varying(20) DEFAULT 'ver'::character varying NOT NULL,
    data_compartilhamento timestamp with time zone DEFAULT now() NOT NULL
);


ALTER TABLE public.tb_compartilhamentos OWNER TO postgres;

--
-- TOC entry 280 (class 1259 OID 46468)
-- Name: tb_compartilhamentos_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.tb_compartilhamentos_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.tb_compartilhamentos_id_seq OWNER TO postgres;

--
-- TOC entry 5583 (class 0 OID 0)
-- Dependencies: 280
-- Name: tb_compartilhamentos_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.tb_compartilhamentos_id_seq OWNED BY public.tb_compartilhamentos.id;


--
-- TOC entry 284 (class 1259 OID 46524)
-- Name: tb_configuracoes; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.tb_configuracoes (
    chave character varying(100) NOT NULL,
    valor text,
    grupo character varying(50) DEFAULT 'geral'::character varying NOT NULL,
    descricao character varying(255),
    atualizado_em timestamp with time zone DEFAULT CURRENT_TIMESTAMP,
    atualizado_por integer
);


ALTER TABLE public.tb_configuracoes OWNER TO postgres;

--
-- TOC entry 269 (class 1259 OID 46345)
-- Name: tb_empresas; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.tb_empresas (
    id integer NOT NULL,
    nome character varying(255) NOT NULL,
    descricao text,
    ativa boolean DEFAULT true NOT NULL,
    criado_por integer,
    data_criacao timestamp with time zone DEFAULT now() NOT NULL,
    data_atualizacao timestamp with time zone DEFAULT now() NOT NULL
);


ALTER TABLE public.tb_empresas OWNER TO postgres;

--
-- TOC entry 268 (class 1259 OID 46344)
-- Name: tb_empresas_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.tb_empresas_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.tb_empresas_id_seq OWNER TO postgres;

--
-- TOC entry 5584 (class 0 OID 0)
-- Dependencies: 268
-- Name: tb_empresas_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.tb_empresas_id_seq OWNED BY public.tb_empresas.id;


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
    data_atualizacao timestamp with time zone DEFAULT now(),
    criado_por integer
);


ALTER TABLE public.tb_eventos_api OWNER TO postgres;

--
-- TOC entry 5585 (class 0 OID 0)
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
-- TOC entry 5586 (class 0 OID 0)
-- Dependencies: 246
-- Name: tb_eventos_api_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.tb_eventos_api_id_seq OWNED BY public.tb_eventos_api.id;


--
-- TOC entry 288 (class 1259 OID 46550)
-- Name: tb_fila_execucao; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.tb_fila_execucao (
    id integer NOT NULL,
    tipo character varying(30) NOT NULL,
    id_recurso integer NOT NULL,
    nome_recurso character varying(255),
    status character varying(20) DEFAULT 'pendente'::character varying NOT NULL,
    prioridade smallint DEFAULT 5 NOT NULL,
    tentativas integer DEFAULT 0 NOT NULL,
    max_tentativas integer DEFAULT 3 NOT NULL,
    agendado_para timestamp with time zone DEFAULT CURRENT_TIMESTAMP,
    iniciado_em timestamp with time zone,
    concluido_em timestamp with time zone,
    resultado jsonb,
    erro text,
    id_usuario integer,
    nome_usuario character varying(255),
    worker_id character varying(100),
    criado_em timestamp with time zone DEFAULT CURRENT_TIMESTAMP
);


ALTER TABLE public.tb_fila_execucao OWNER TO postgres;

--
-- TOC entry 287 (class 1259 OID 46549)
-- Name: tb_fila_execucao_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.tb_fila_execucao_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.tb_fila_execucao_id_seq OWNER TO postgres;

--
-- TOC entry 5587 (class 0 OID 0)
-- Dependencies: 287
-- Name: tb_fila_execucao_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.tb_fila_execucao_id_seq OWNED BY public.tb_fila_execucao.id;


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
-- TOC entry 5588 (class 0 OID 0)
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
-- TOC entry 5589 (class 0 OID 0)
-- Dependencies: 237
-- Name: tb_metricas_sistema_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.tb_metricas_sistema_id_seq OWNED BY public.tb_metricas_sistema.id;


--
-- TOC entry 265 (class 1259 OID 46308)
-- Name: tb_notificacoes; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.tb_notificacoes (
    id integer NOT NULL,
    tipo character varying(50) DEFAULT 'system'::character varying NOT NULL,
    titulo character varying(255) NOT NULL,
    mensagem text,
    dados jsonb DEFAULT '{}'::jsonb,
    lida boolean DEFAULT false,
    id_usuario integer,
    created_at timestamp without time zone DEFAULT now()
);


ALTER TABLE public.tb_notificacoes OWNER TO postgres;

--
-- TOC entry 264 (class 1259 OID 46307)
-- Name: tb_notificacoes_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.tb_notificacoes_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.tb_notificacoes_id_seq OWNER TO postgres;

--
-- TOC entry 5590 (class 0 OID 0)
-- Dependencies: 264
-- Name: tb_notificacoes_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.tb_notificacoes_id_seq OWNED BY public.tb_notificacoes.id;


--
-- TOC entry 294 (class 1259 OID 46602)
-- Name: tb_password_resets; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.tb_password_resets (
    id bigint NOT NULL,
    id_usuario bigint NOT NULL,
    token_hash text NOT NULL,
    chave_hex character varying(6) NOT NULL,
    criado_em timestamp with time zone DEFAULT now() NOT NULL,
    expira_em timestamp with time zone NOT NULL,
    usado boolean DEFAULT false NOT NULL
);


ALTER TABLE public.tb_password_resets OWNER TO postgres;

--
-- TOC entry 293 (class 1259 OID 46601)
-- Name: tb_password_resets_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

ALTER TABLE public.tb_password_resets ALTER COLUMN id ADD GENERATED ALWAYS AS IDENTITY (
    SEQUENCE NAME public.tb_password_resets_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1
);


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
    data_criacao timestamp with time zone DEFAULT now() NOT NULL,
    criado_por integer
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
-- TOC entry 5591 (class 0 OID 0)
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
-- TOC entry 5592 (class 0 OID 0)
-- Dependencies: 260
-- Name: tb_pipelines_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.tb_pipelines_id_seq OWNED BY public.tb_pipelines.id;


--
-- TOC entry 296 (class 1259 OID 46705)
-- Name: tb_politica_retencao_arquivos; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.tb_politica_retencao_arquivos (
    id integer NOT NULL,
    id_rotina integer NOT NULL,
    dias_retencao integer DEFAULT 30 NOT NULL,
    ativo boolean DEFAULT true,
    criado_por integer,
    criado_em timestamp with time zone DEFAULT now(),
    atualizado_em timestamp with time zone DEFAULT now()
);


ALTER TABLE public.tb_politica_retencao_arquivos OWNER TO postgres;

--
-- TOC entry 295 (class 1259 OID 46704)
-- Name: tb_politica_retencao_arquivos_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.tb_politica_retencao_arquivos_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.tb_politica_retencao_arquivos_id_seq OWNER TO postgres;

--
-- TOC entry 5593 (class 0 OID 0)
-- Dependencies: 295
-- Name: tb_politica_retencao_arquivos_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.tb_politica_retencao_arquivos_id_seq OWNED BY public.tb_politica_retencao_arquivos.id;


--
-- TOC entry 271 (class 1259 OID 46365)
-- Name: tb_projetos; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.tb_projetos (
    id integer NOT NULL,
    nome character varying(255) NOT NULL,
    descricao text,
    id_empresa integer NOT NULL,
    ativo boolean DEFAULT true NOT NULL,
    criado_por integer,
    data_criacao timestamp with time zone DEFAULT now() NOT NULL,
    data_atualizacao timestamp with time zone DEFAULT now() NOT NULL
);


ALTER TABLE public.tb_projetos OWNER TO postgres;

--
-- TOC entry 270 (class 1259 OID 46364)
-- Name: tb_projetos_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.tb_projetos_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.tb_projetos_id_seq OWNER TO postgres;

--
-- TOC entry 5594 (class 0 OID 0)
-- Dependencies: 270
-- Name: tb_projetos_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.tb_projetos_id_seq OWNED BY public.tb_projetos.id;


--
-- TOC entry 267 (class 1259 OID 46324)
-- Name: tb_rate_limits; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.tb_rate_limits (
    id integer NOT NULL,
    chave character varying(255) NOT NULL,
    tentativas integer DEFAULT 1,
    primeira_tentativa timestamp without time zone DEFAULT now(),
    ultima_tentativa timestamp without time zone DEFAULT now()
);


ALTER TABLE public.tb_rate_limits OWNER TO postgres;

--
-- TOC entry 266 (class 1259 OID 46323)
-- Name: tb_rate_limits_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.tb_rate_limits_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.tb_rate_limits_id_seq OWNER TO postgres;

--
-- TOC entry 5595 (class 0 OID 0)
-- Dependencies: 266
-- Name: tb_rate_limits_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.tb_rate_limits_id_seq OWNED BY public.tb_rate_limits.id;


--
-- TOC entry 277 (class 1259 OID 46435)
-- Name: tb_recurso_empresas; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.tb_recurso_empresas (
    id integer NOT NULL,
    tipo_recurso character varying(50) NOT NULL,
    id_recurso integer NOT NULL,
    id_empresa integer NOT NULL,
    data_associacao timestamp with time zone DEFAULT now() NOT NULL
);


ALTER TABLE public.tb_recurso_empresas OWNER TO postgres;

--
-- TOC entry 276 (class 1259 OID 46434)
-- Name: tb_recurso_empresas_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.tb_recurso_empresas_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.tb_recurso_empresas_id_seq OWNER TO postgres;

--
-- TOC entry 5596 (class 0 OID 0)
-- Dependencies: 276
-- Name: tb_recurso_empresas_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.tb_recurso_empresas_id_seq OWNED BY public.tb_recurso_empresas.id;


--
-- TOC entry 279 (class 1259 OID 46452)
-- Name: tb_recurso_projetos; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.tb_recurso_projetos (
    id integer NOT NULL,
    tipo_recurso character varying(50) NOT NULL,
    id_recurso integer NOT NULL,
    id_projeto integer NOT NULL,
    data_associacao timestamp with time zone DEFAULT now() NOT NULL
);


ALTER TABLE public.tb_recurso_projetos OWNER TO postgres;

--
-- TOC entry 278 (class 1259 OID 46451)
-- Name: tb_recurso_projetos_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.tb_recurso_projetos_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.tb_recurso_projetos_id_seq OWNER TO postgres;

--
-- TOC entry 5597 (class 0 OID 0)
-- Dependencies: 278
-- Name: tb_recurso_projetos_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.tb_recurso_projetos_id_seq OWNED BY public.tb_recurso_projetos.id;


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
    notificar_falha boolean DEFAULT true,
    parar_em_erro boolean DEFAULT false,
    rollback_em_erro boolean DEFAULT false
);


ALTER TABLE public.tb_rotinas OWNER TO postgres;

--
-- TOC entry 5598 (class 0 OID 0)
-- Dependencies: 230
-- Name: COLUMN tb_rotinas.data_inicio; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.tb_rotinas.data_inicio IS 'Data e hora de início do agendamento (quando começar a executar)';


--
-- TOC entry 5599 (class 0 OID 0)
-- Dependencies: 230
-- Name: COLUMN tb_rotinas.data_fim; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.tb_rotinas.data_fim IS 'Data e hora de término do agendamento (quando parar de executar)';


--
-- TOC entry 5600 (class 0 OID 0)
-- Dependencies: 230
-- Name: COLUMN tb_rotinas.datas_ignorar_json; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.tb_rotinas.datas_ignorar_json IS 'Array JSON com datas específicas para não executar';


--
-- TOC entry 5601 (class 0 OID 0)
-- Dependencies: 230
-- Name: COLUMN tb_rotinas.ignorar_feriados; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.tb_rotinas.ignorar_feriados IS 'Se deve ignorar feriados nacionais brasileiros';


--
-- TOC entry 5602 (class 0 OID 0)
-- Dependencies: 230
-- Name: COLUMN tb_rotinas.timeout; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.tb_rotinas.timeout IS 'Timeout máximo de execução em segundos (padrão: 300s = 5min)';


--
-- TOC entry 5603 (class 0 OID 0)
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
-- TOC entry 273 (class 1259 OID 46391)
-- Name: tb_usuario_empresas; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.tb_usuario_empresas (
    id integer NOT NULL,
    id_usuario integer NOT NULL,
    id_empresa integer NOT NULL,
    data_associacao timestamp with time zone DEFAULT now() NOT NULL
);


ALTER TABLE public.tb_usuario_empresas OWNER TO postgres;

--
-- TOC entry 272 (class 1259 OID 46390)
-- Name: tb_usuario_empresas_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.tb_usuario_empresas_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.tb_usuario_empresas_id_seq OWNER TO postgres;

--
-- TOC entry 5604 (class 0 OID 0)
-- Dependencies: 272
-- Name: tb_usuario_empresas_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.tb_usuario_empresas_id_seq OWNED BY public.tb_usuario_empresas.id;


--
-- TOC entry 275 (class 1259 OID 46413)
-- Name: tb_usuario_projetos; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.tb_usuario_projetos (
    id integer NOT NULL,
    id_usuario integer NOT NULL,
    id_projeto integer NOT NULL,
    data_associacao timestamp with time zone DEFAULT now() NOT NULL
);


ALTER TABLE public.tb_usuario_projetos OWNER TO postgres;

--
-- TOC entry 274 (class 1259 OID 46412)
-- Name: tb_usuario_projetos_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.tb_usuario_projetos_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.tb_usuario_projetos_id_seq OWNER TO postgres;

--
-- TOC entry 5605 (class 0 OID 0)
-- Dependencies: 274
-- Name: tb_usuario_projetos_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.tb_usuario_projetos_id_seq OWNED BY public.tb_usuario_projetos.id;


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
    ldap_auth boolean DEFAULT false,
    nome text,
    email text,
    cpf text,
    bloqueado_ate timestamp with time zone
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
-- TOC entry 5606 (class 0 OID 0)
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
-- TOC entry 5607 (class 0 OID 0)
-- Dependencies: 248
-- Name: tb_valores_capturados_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.tb_valores_capturados_id_seq OWNED BY public.tb_valores_capturados.id;


--
-- TOC entry 286 (class 1259 OID 46535)
-- Name: tb_webhooks; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.tb_webhooks (
    id integer NOT NULL,
    nome character varying(255) NOT NULL,
    url character varying(500) NOT NULL,
    eventos text[] DEFAULT '{}'::text[] NOT NULL,
    headers jsonb DEFAULT '{}'::jsonb,
    ativo boolean DEFAULT true,
    secret character varying(255),
    criado_por integer,
    criado_em timestamp with time zone DEFAULT CURRENT_TIMESTAMP,
    atualizado_em timestamp with time zone DEFAULT CURRENT_TIMESTAMP
);


ALTER TABLE public.tb_webhooks OWNER TO postgres;

--
-- TOC entry 285 (class 1259 OID 46534)
-- Name: tb_webhooks_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.tb_webhooks_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.tb_webhooks_id_seq OWNER TO postgres;

--
-- TOC entry 5608 (class 0 OID 0)
-- Dependencies: 285
-- Name: tb_webhooks_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.tb_webhooks_id_seq OWNED BY public.tb_webhooks.id;


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
-- TOC entry 5609 (class 0 OID 0)
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
-- TOC entry 5610 (class 0 OID 0)
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
-- TOC entry 5611 (class 0 OID 0)
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
-- TOC entry 5612 (class 0 OID 0)
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
-- TOC entry 5613 (class 0 OID 0)
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
-- TOC entry 5614 (class 0 OID 0)
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
-- TOC entry 5615 (class 0 OID 0)
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
-- TOC entry 5616 (class 0 OID 0)
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
-- TOC entry 5617 (class 0 OID 0)
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
-- TOC entry 5618 (class 0 OID 0)
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
-- TOC entry 5619 (class 0 OID 0)
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
-- TOC entry 4946 (class 2604 OID 45523)
-- Name: connections id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.connections ALTER COLUMN id SET DEFAULT nextval('public.connections_id_seq'::regclass);


--
-- TOC entry 4982 (class 2604 OID 45680)
-- Name: logs_sistema id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.logs_sistema ALTER COLUMN id SET DEFAULT nextval('public.logs_sistema_id_seq'::regclass);


--
-- TOC entry 4947 (class 2604 OID 45532)
-- Name: schedules id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.schedules ALTER COLUMN id SET DEFAULT nextval('public.schedules_id_seq'::regclass);


--
-- TOC entry 4985 (class 2604 OID 45714)
-- Name: tb_api_externas id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tb_api_externas ALTER COLUMN id SET DEFAULT nextval('public.tb_api_externas_id_seq'::regclass);


--
-- TOC entry 4943 (class 2604 OID 45488)
-- Name: tb_arquivos id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tb_arquivos ALTER COLUMN id SET DEFAULT nextval('public.tb_arquivos_id_seq'::regclass);


--
-- TOC entry 5093 (class 2604 OID 46511)
-- Name: tb_auditoria id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tb_auditoria ALTER COLUMN id SET DEFAULT nextval('public.tb_auditoria_id_seq'::regclass);


--
-- TOC entry 4945 (class 2604 OID 45498)
-- Name: tb_auditoria_rotina id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tb_auditoria_rotina ALTER COLUMN id SET DEFAULT nextval('public.tb_auditoria_rotina_id_seq'::regclass);


--
-- TOC entry 5120 (class 2604 OID 46591)
-- Name: tb_backups id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tb_backups ALTER COLUMN id SET DEFAULT nextval('public.tb_backups_id_seq'::regclass);


--
-- TOC entry 5112 (class 2604 OID 46573)
-- Name: tb_canais_notificacao id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tb_canais_notificacao ALTER COLUMN id SET DEFAULT nextval('public.tb_canais_notificacao_id_seq'::regclass);


--
-- TOC entry 5090 (class 2604 OID 46472)
-- Name: tb_compartilhamentos id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tb_compartilhamentos ALTER COLUMN id SET DEFAULT nextval('public.tb_compartilhamentos_id_seq'::regclass);


--
-- TOC entry 5074 (class 2604 OID 46348)
-- Name: tb_empresas id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tb_empresas ALTER COLUMN id SET DEFAULT nextval('public.tb_empresas_id_seq'::regclass);


--
-- TOC entry 4996 (class 2604 OID 45735)
-- Name: tb_eventos_api id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tb_eventos_api ALTER COLUMN id SET DEFAULT nextval('public.tb_eventos_api_id_seq'::regclass);


--
-- TOC entry 5105 (class 2604 OID 46553)
-- Name: tb_fila_execucao id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tb_fila_execucao ALTER COLUMN id SET DEFAULT nextval('public.tb_fila_execucao_id_seq'::regclass);


--
-- TOC entry 4970 (class 2604 OID 45635)
-- Name: tb_logs_sistema id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tb_logs_sistema ALTER COLUMN id SET DEFAULT nextval('public.tb_logs_sistema_id_seq'::regclass);


--
-- TOC entry 4974 (class 2604 OID 45650)
-- Name: tb_metricas_sistema id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tb_metricas_sistema ALTER COLUMN id SET DEFAULT nextval('public.tb_metricas_sistema_id_seq'::regclass);


--
-- TOC entry 5065 (class 2604 OID 46311)
-- Name: tb_notificacoes id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tb_notificacoes ALTER COLUMN id SET DEFAULT nextval('public.tb_notificacoes_id_seq'::regclass);


--
-- TOC entry 5056 (class 2604 OID 46281)
-- Name: tb_pipeline_execucoes id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tb_pipeline_execucoes ALTER COLUMN id SET DEFAULT nextval('public.tb_pipeline_execucoes_id_seq'::regclass);


--
-- TOC entry 5044 (class 2604 OID 46261)
-- Name: tb_pipelines id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tb_pipelines ALTER COLUMN id SET DEFAULT nextval('public.tb_pipelines_id_seq'::regclass);


--
-- TOC entry 5126 (class 2604 OID 46708)
-- Name: tb_politica_retencao_arquivos id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tb_politica_retencao_arquivos ALTER COLUMN id SET DEFAULT nextval('public.tb_politica_retencao_arquivos_id_seq'::regclass);


--
-- TOC entry 5078 (class 2604 OID 46368)
-- Name: tb_projetos id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tb_projetos ALTER COLUMN id SET DEFAULT nextval('public.tb_projetos_id_seq'::regclass);


--
-- TOC entry 5070 (class 2604 OID 46327)
-- Name: tb_rate_limits id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tb_rate_limits ALTER COLUMN id SET DEFAULT nextval('public.tb_rate_limits_id_seq'::regclass);


--
-- TOC entry 5086 (class 2604 OID 46438)
-- Name: tb_recurso_empresas id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tb_recurso_empresas ALTER COLUMN id SET DEFAULT nextval('public.tb_recurso_empresas_id_seq'::regclass);


--
-- TOC entry 5088 (class 2604 OID 46455)
-- Name: tb_recurso_projetos id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tb_recurso_projetos ALTER COLUMN id SET DEFAULT nextval('public.tb_recurso_projetos_id_seq'::regclass);


--
-- TOC entry 5082 (class 2604 OID 46394)
-- Name: tb_usuario_empresas id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tb_usuario_empresas ALTER COLUMN id SET DEFAULT nextval('public.tb_usuario_empresas_id_seq'::regclass);


--
-- TOC entry 5084 (class 2604 OID 46416)
-- Name: tb_usuario_projetos id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tb_usuario_projetos ALTER COLUMN id SET DEFAULT nextval('public.tb_usuario_projetos_id_seq'::regclass);


--
-- TOC entry 5004 (class 2604 OID 45759)
-- Name: tb_valores_capturados id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tb_valores_capturados ALTER COLUMN id SET DEFAULT nextval('public.tb_valores_capturados_id_seq'::regclass);


--
-- TOC entry 5099 (class 2604 OID 46538)
-- Name: tb_webhooks id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tb_webhooks ALTER COLUMN id SET DEFAULT nextval('public.tb_webhooks_id_seq'::regclass);


--
-- TOC entry 4977 (class 2604 OID 45661)
-- Name: tb_worker_heartbeat id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tb_worker_heartbeat ALTER COLUMN id SET DEFAULT nextval('public.tb_worker_heartbeat_id_seq'::regclass);


--
-- TOC entry 5022 (class 2604 OID 45825)
-- Name: tb_workflow_edges id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tb_workflow_edges ALTER COLUMN id SET DEFAULT nextval('public.tb_workflow_edges_id_seq'::regclass);


--
-- TOC entry 5026 (class 2604 OID 45847)
-- Name: tb_workflow_execucoes id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tb_workflow_execucoes ALTER COLUMN id SET DEFAULT nextval('public.tb_workflow_execucoes_id_seq'::regclass);


--
-- TOC entry 5038 (class 2604 OID 45875)
-- Name: tb_workflow_node_execucoes id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tb_workflow_node_execucoes ALTER COLUMN id SET DEFAULT nextval('public.tb_workflow_node_execucoes_id_seq'::regclass);


--
-- TOC entry 5016 (class 2604 OID 45802)
-- Name: tb_workflow_nodes id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tb_workflow_nodes ALTER COLUMN id SET DEFAULT nextval('public.tb_workflow_nodes_id_seq'::regclass);


--
-- TOC entry 5008 (class 2604 OID 45784)
-- Name: tb_workflows id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tb_workflows ALTER COLUMN id SET DEFAULT nextval('public.tb_workflows_id_seq'::regclass);


--
-- TOC entry 5493 (class 0 OID 45520)
-- Dependencies: 222
-- Data for Name: connections; Type: TABLE DATA; Schema: public; Owner: postgres
--

INSERT INTO public.connections VALUES (1, 'local_sqlite', 'sqlite', NULL, '', 'C:\Users\caio.barros\OneDrive\Cloud\PESSOAL\CAIO\NOTEBOOK\PROJETOS\DMC-DATALOAD\backend\test_target.db', NULL, NULL, '', '{"driver": "sqlite", "database": "C:\\Users\\caio.barros\\OneDrive\\Cloud\\PESSOAL\\CAIO\\NOTEBOOK\\PROJETOS\\DMC-DATALOAD\\backend\\test_target.db"}');


--
-- TOC entry 5513 (class 0 OID 45677)
-- Dependencies: 243
-- Data for Name: logs_sistema; Type: TABLE DATA; Schema: public; Owner: postgres
--



--
-- TOC entry 5495 (class 0 OID 45529)
-- Dependencies: 224
-- Data for Name: schedules; Type: TABLE DATA; Schema: public; Owner: postgres
--



--
-- TOC entry 5515 (class 0 OID 45711)
-- Dependencies: 245
-- Data for Name: tb_api_externas; Type: TABLE DATA; Schema: public; Owner: postgres
--



--
-- TOC entry 5489 (class 0 OID 45485)
-- Dependencies: 218
-- Data for Name: tb_arquivos; Type: TABLE DATA; Schema: public; Owner: postgres
--



--
-- TOC entry 5553 (class 0 OID 46508)
-- Dependencies: 283
-- Data for Name: tb_auditoria; Type: TABLE DATA; Schema: public; Owner: postgres
--

INSERT INTO public.tb_auditoria VALUES (90, 'editar', 'usuario', 112, 'renan', 1, 'admin', 'super_admin', '[]', '{"nivel_acesso": "desenvolvedor"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-26 13:37:12.972082-03');
INSERT INTO public.tb_auditoria VALUES (91, 'executar_sql', 'sql_editor', 2, 'SGIC - HOMOLOGAÇÃO', 1, 'admin', 'super_admin', '[]', '{"sql": "SELECT CPF,\n       NOME\n  FROM SCH_SGIC.SGIC_USUARIO U\n WHERE U.IND_ATIVO = FALSE\n   AND EXISTS (SELECT X.CPF\n                 FROM SCH_SGIC.AUX_USUARIO_OUTRAS_ENTIDADES X\n                WHERE U.CPF = X.CPF)\nLIMIT 100 OFFSET 0", "erro": "SQLSTATE[42P01]: Undefined table: 7 ERRO:  relação \"sch_sgic.aux_usuario_outras_entidades\" não existe\nLINE 6:                  FROM SCH_SGIC.AUX_USUARIO_OUTRAS_ENTIDADES ...\n                              ^", "tipo": "ERRO"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-26 13:40:05.805542-03');
INSERT INTO public.tb_auditoria VALUES (92, 'executar_sql', 'sql_editor', 2, 'SGIC - HOMOLOGAÇÃO', 1, 'admin', 'super_admin', '[]', '{"sql": "SELECT CPF,\n       NOME\n  FROM SCH_SGIC.SGIC_USUARIO U\n\nLIMIT 100 OFFSET 0", "tipo": "SELECT", "linhas": 100}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-26 13:40:27.544734-03');
INSERT INTO public.tb_auditoria VALUES (93, 'criar', 'rotina', 6, 'CARGA | MENSAL: 2 | HOMOLOGAÇÃO', 1, 'admin', 'super_admin', '[]', '[]', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-26 16:45:33.779116-03');
INSERT INTO public.tb_auditoria VALUES (94, 'editar', 'rotina', 6, 'CARGA | MENSAL: 2 | HOMOLOGAÇÃO', 1, 'admin', 'super_admin', '[]', '[]', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-26 16:48:49.892852-03');
INSERT INTO public.tb_auditoria VALUES (95, 'login', 'sessao', 1, 'admin', 1, 'admin', 'super_admin', '[]', '[]', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-27 08:46:38.492687-03');
INSERT INTO public.tb_auditoria VALUES (96, 'login', 'sessao', 1, 'admin', 1, 'admin', 'super_admin', '[]', '[]', '::1', 'curl/8.14.1', '2026-03-27 09:09:41.576007-03');
INSERT INTO public.tb_auditoria VALUES (97, 'login', 'sessao', 1, 'admin', 1, 'admin', 'super_admin', '[]', '[]', '::1', 'curl/8.14.1', '2026-03-27 09:44:26.119033-03');
INSERT INTO public.tb_auditoria VALUES (98, 'login', 'sessao', 3, 'leonardo', 3, 'leonardo', 'desenvolvedor', '[]', '[]', '10.1.110.27', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:149.0) Gecko/20100101 Firefox/149.0', '2026-03-27 10:21:04.079758-03');
INSERT INTO public.tb_auditoria VALUES (99, 'editar', 'usuario', 3, 'leonardo', 1, 'admin', 'super_admin', '[]', '{"nivel_acesso": "desenvolvedor"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-27 10:21:43.235204-03');
INSERT INTO public.tb_auditoria VALUES (100, 'login', 'sessao', 1, 'admin', 1, 'admin', 'super_admin', '[]', '[]', '::1', 'curl/8.14.1', '2026-03-27 10:38:14.742302-03');
INSERT INTO public.tb_auditoria VALUES (101, 'login', 'sessao', 32, 'lucas', 32, 'lucas', 'desenvolvedor', '[]', '[]', '10.1.110.9', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:149.0) Gecko/20100101 Firefox/149.0', '2026-03-27 10:50:22.305648-03');
INSERT INTO public.tb_auditoria VALUES (102, 'editar', 'conexao', 8, '', 1, 'admin', 'super_admin', '[]', '[]', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-27 10:57:52.001648-03');
INSERT INTO public.tb_auditoria VALUES (103, 'editar', 'conexao', 7, '', 1, 'admin', 'super_admin', '[]', '[]', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-27 10:58:07.157725-03');
INSERT INTO public.tb_auditoria VALUES (104, 'editar', 'usuario', 112, 'renan', 1, 'admin', 'super_admin', '[]', '{"nivel_acesso": "desenvolvedor"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-27 10:58:43.103972-03');
INSERT INTO public.tb_auditoria VALUES (105, 'editar', 'usuario', 3, 'leonardo', 1, 'admin', 'super_admin', '[]', '{"nivel_acesso": "desenvolvedor"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-27 10:58:58.827238-03');
INSERT INTO public.tb_auditoria VALUES (106, 'editar', 'usuario', 112, 'renan', 1, 'admin', 'super_admin', '[]', '{"nivel_acesso": "desenvolvedor"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-27 10:59:07.891959-03');
INSERT INTO public.tb_auditoria VALUES (107, 'editar', 'usuario', 32, 'lucas', 1, 'admin', 'super_admin', '[]', '{"nivel_acesso": "desenvolvedor"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-27 10:59:33.681912-03');
INSERT INTO public.tb_auditoria VALUES (108, 'editar', 'usuario', 111, 'caio', 1, 'admin', 'super_admin', '[]', '{"nivel_acesso": "desenvolvedor"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-27 10:59:45.221519-03');
INSERT INTO public.tb_auditoria VALUES (109, 'editar', 'usuario', 3, 'leonardo', 1, 'admin', 'super_admin', '[]', '{"nivel_acesso": "desenvolvedor"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-27 10:59:59.759502-03');
INSERT INTO public.tb_auditoria VALUES (110, 'editar', 'usuario', 112, 'renan', 1, 'admin', 'super_admin', '[]', '{"nivel_acesso": "desenvolvedor"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-27 11:00:06.603982-03');
INSERT INTO public.tb_auditoria VALUES (111, 'editar', 'usuario', 32, 'lucas', 1, 'admin', 'super_admin', '[]', '{"nivel_acesso": "desenvolvedor"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-27 11:00:42.199578-03');
INSERT INTO public.tb_auditoria VALUES (112, 'editar', 'conexao', 1, '', 1, 'admin', 'super_admin', '[]', '[]', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-27 11:01:27.732269-03');
INSERT INTO public.tb_auditoria VALUES (113, 'login', 'sessao', 112, 'renan', 112, 'renan', 'desenvolvedor', '[]', '[]', '10.1.1.68', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-27 13:06:00.531042-03');
INSERT INTO public.tb_auditoria VALUES (114, 'login', 'sessao', 1, 'admin', 1, 'admin', 'super_admin', '[]', '[]', '::1', 'curl/8.14.1', '2026-03-27 13:21:54.21203-03');
INSERT INTO public.tb_auditoria VALUES (115, 'criar', 'conexao', 0, '', 1, 'admin', 'super_admin', '[]', '[]', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-27 13:45:49.967576-03');
INSERT INTO public.tb_auditoria VALUES (116, 'criar', 'conexao', 0, '', 1, 'admin', 'super_admin', '[]', '[]', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-27 13:46:21.734293-03');
INSERT INTO public.tb_auditoria VALUES (117, 'login', 'sessao', 1, 'admin', 1, 'admin', 'super_admin', '[]', '[]', '::1', 'curl/8.14.1', '2026-03-27 13:52:34.870461-03');
INSERT INTO public.tb_auditoria VALUES (118, 'login', 'sessao', 1, 'admin', 1, 'admin', 'super_admin', '[]', '[]', '::1', 'curl/8.14.1', '2026-03-27 14:04:23.401543-03');
INSERT INTO public.tb_auditoria VALUES (119, 'login', 'sessao', 1, 'admin', 1, 'admin', 'super_admin', '[]', '[]', '::1', 'curl/8.14.1', '2026-03-27 14:05:37.308515-03');
INSERT INTO public.tb_auditoria VALUES (120, 'login', 'sessao', 1, 'admin', 1, 'admin', 'super_admin', '[]', '[]', '::1', 'curl/8.14.1', '2026-03-27 14:14:30.191505-03');
INSERT INTO public.tb_auditoria VALUES (121, 'login', 'sessao', 112, 'renan', 112, 'renan', 'desenvolvedor', '[]', '[]', '10.1.1.68', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-27 14:22:48.484355-03');
INSERT INTO public.tb_auditoria VALUES (122, 'login', 'sessao', 1, 'admin', 1, 'admin', 'super_admin', '[]', '[]', '::1', 'curl/8.14.1', '2026-03-27 14:33:00.768847-03');
INSERT INTO public.tb_auditoria VALUES (123, 'login', 'sessao', 1, 'admin', 1, 'admin', 'super_admin', '[]', '[]', '::1', 'curl/8.14.1', '2026-03-27 14:39:56.612294-03');
INSERT INTO public.tb_auditoria VALUES (124, 'login', 'sessao', 1, 'admin', 1, 'admin', 'super_admin', '[]', '[]', '::1', 'curl/8.14.1', '2026-03-27 14:48:50.252058-03');
INSERT INTO public.tb_auditoria VALUES (125, 'login', 'sessao', 1, 'admin', 1, 'admin', 'super_admin', '[]', '[]', '::1', 'curl/8.14.1', '2026-03-27 14:55:23.203752-03');
INSERT INTO public.tb_auditoria VALUES (126, 'login', 'sessao', 1, 'admin', 1, 'admin', 'super_admin', '[]', '[]', '::1', 'curl/8.14.1', '2026-03-27 14:58:52.161097-03');
INSERT INTO public.tb_auditoria VALUES (127, 'login', 'sessao', 1, 'admin', 1, 'admin', 'super_admin', '[]', '[]', '::1', 'curl/8.14.1', '2026-03-27 15:04:48.453466-03');
INSERT INTO public.tb_auditoria VALUES (128, 'login', 'sessao', 1, 'admin', 1, 'admin', 'super_admin', '[]', '[]', '::1', 'curl/8.14.1', '2026-03-27 15:07:17.933968-03');
INSERT INTO public.tb_auditoria VALUES (129, 'login', 'sessao', 1, 'admin', 1, 'admin', 'super_admin', '[]', '[]', '::1', 'curl/8.14.1', '2026-03-27 15:13:11.515542-03');
INSERT INTO public.tb_auditoria VALUES (130, 'login', 'sessao', 1, 'admin', 1, 'admin', 'super_admin', '[]', '[]', '::1', 'curl/8.14.1', '2026-03-27 16:05:06.533564-03');
INSERT INTO public.tb_auditoria VALUES (131, 'login', 'sessao', 1, 'admin', 1, 'admin', 'super_admin', '[]', '[]', '::1', 'curl/8.14.1', '2026-03-27 16:12:31.859772-03');


--
-- TOC entry 5491 (class 0 OID 45495)
-- Dependencies: 220
-- Data for Name: tb_auditoria_rotina; Type: TABLE DATA; Schema: public; Owner: postgres
--



--
-- TOC entry 5562 (class 0 OID 46588)
-- Dependencies: 292
-- Data for Name: tb_backups; Type: TABLE DATA; Schema: public; Owner: postgres
--



--
-- TOC entry 5503 (class 0 OID 45592)
-- Dependencies: 232
-- Data for Name: tb_blocos_rotina; Type: TABLE DATA; Schema: public; Owner: postgres
--

INSERT INTO public.tb_blocos_rotina OVERRIDING SYSTEM VALUE VALUES (619, 5, '1', 1, 'DROP TABLE IF EXISTS SCH_SGIC.AUX_USUARIO_OUTRAS_ENTIDADES;', 'DROP', '2026-03-25 15:24:39.179426-03');
INSERT INTO public.tb_blocos_rotina OVERRIDING SYSTEM VALUE VALUES (620, 5, '2', 2, 'CREATE TABLE IF NOT EXISTS SCH_SGIC.AUX_USUARIO_OUTRAS_ENTIDADES
(
  id bigserial,
  ID_OUTRAS_ENTIDADES integer NOT NULL,
  CPF character varying(11),
  MATRICULA character varying(11),
  EMAIL character varying(50),
  NOME character varying(100),
  TELEFONE character varying(20),
  MATRICULA_CHEFIA character varying(11),
  EMAIL_CHEFIA character varying(50)
)
WITH (
  OIDS=FALSE
);', 'CREATE', '2026-03-25 15:24:39.181602-03');
INSERT INTO public.tb_blocos_rotina OVERRIDING SYSTEM VALUE VALUES (621, 5, '3', 3, '--Caso retorne algum erro informando que esteja faltando o ID_OUTRAS_ENTIDADES, deve ser realizado o backup da tabela sch_sgic.sgic_outras_entidades do ambiente de produção e restaurar em homologação.
DROP TABLE IF EXISTS sch_sgic.bkp_sgic_outras_entidades;', 'DROP', '2026-03-25 15:24:39.183004-03');
INSERT INTO public.tb_blocos_rotina OVERRIDING SYSTEM VALUE VALUES (622, 5, '4', 4, 'CREATE TABLE IF NOT EXISTS sch_sgic.bkp_sgic_outras_entidades AS SELECT * FROM sch_sgic.sgic_outras_entidades;', 'CREATE', '2026-03-25 15:24:39.184244-03');
INSERT INTO public.tb_blocos_rotina OVERRIDING SYSTEM VALUE VALUES (623, 5, '5', 5, 'INSERT INTO sch_sgic.sgic_outras_entidades
SELECT id_outras_entidades,
       data_alteracao,
       data_exclusao,
       data_inclusao,
       ativo,
       esfera,
       descricao,
       poder,
       sigla,
       tipo,
       ind_prefeitura
  FROM sch_sgic.bkp_sgic_outras_entidades B
  WHERE NOT EXISTS (SELECT 1
      FROM SCH_SGIC.SGIC_OUTRAS_ENTIDADES O
     WHERE O.ID_OUTRAS_ENTIDADES = B.ID_OUTRAS_ENTIDADES);', 'INSERT', '2026-03-25 15:24:39.185399-03');
INSERT INTO public.tb_blocos_rotina OVERRIDING SYSTEM VALUE VALUES (624, 5, '6', 6, 'SELECT COUNT(*) FROM SCH_SGIC.AUX_USUARIO_OUTRAS_ENTIDADES
WHERE CPF IN
(''00000000000'',''11111111111'',''22222222222'',''33333333333'',''44444444444'',''55555555555'',
 ''66666666666'',''77777777777'',''88888888888'',''99999999999'',''99999999991'');', 'SELECT', '2026-03-25 15:24:39.186545-03');
INSERT INTO public.tb_blocos_rotina OVERRIDING SYSTEM VALUE VALUES (625, 5, '7', 7, 'DELETE FROM SCH_SGIC.AUX_USUARIO_OUTRAS_ENTIDADES
WHERE CPF IN
(''00000000000'',''11111111111'',''22222222222'',''33333333333'',''44444444444'',''55555555555'',
 ''66666666666'',''77777777777'',''88888888888'',''99999999999'',''99999999991'');', 'SELECT', '2026-03-25 15:24:39.187327-03');
INSERT INTO public.tb_blocos_rotina OVERRIDING SYSTEM VALUE VALUES (626, 5, '8', 8, 'SELECT CPF,COUNT(*)
  FROM SCH_SGIC.AUX_USUARIO_OUTRAS_ENTIDADES
 GROUP BY CPF
HAVING COUNT(*) > 1;', 'SELECT', '2026-03-25 15:24:39.188009-03');
INSERT INTO public.tb_blocos_rotina OVERRIDING SYSTEM VALUE VALUES (627, 5, '9', 9, 'SELECT * FROM SCH_SGIC.AUX_USUARIO_OUTRAS_ENTIDADES
 WHERE ID IN (SELECT MIN(ID)
          FROM SCH_SGIC.AUX_USUARIO_OUTRAS_ENTIDADES
      GROUP BY CPF
     HAVING COUNT(*) > 1);', 'SELECT', '2026-03-25 15:24:39.188505-03');
INSERT INTO public.tb_blocos_rotina OVERRIDING SYSTEM VALUE VALUES (628, 5, '10', 10, 'DELETE FROM SCH_SGIC.AUX_USUARIO_OUTRAS_ENTIDADES
 WHERE ID IN (SELECT MIN(ID)
          FROM SCH_SGIC.AUX_USUARIO_OUTRAS_ENTIDADES
      GROUP BY CPF
     HAVING COUNT(*) > 1);', 'DELETE', '2026-03-25 15:24:39.18894-03');
INSERT INTO public.tb_blocos_rotina OVERRIDING SYSTEM VALUE VALUES (629, 5, '11', 11, '-- VERIFICA INEXISTENCIA DE 11 CARACTERES PARA O CPF
-- MIN = 11
SELECT 
	MIN(CHARACTER_LENGTH(CPF))
 FROM
 	SCH_SGIC.AUX_USUARIO_OUTRAS_ENTIDADES;', 'SELECT', '2026-03-25 15:24:39.189381-03');
INSERT INTO public.tb_blocos_rotina OVERRIDING SYSTEM VALUE VALUES (630, 5, '12', 12, 'UPDATE 
	SCH_SGIC.AUX_USUARIO_OUTRAS_ENTIDADES
SET
	CPF = LPAD(TRIM(CPF),11,''0'')
WHERE
	CHARACTER_LENGTH (TRIM(CPF)) < 11;', 'UPDATE', '2026-03-25 15:24:39.189867-03');
INSERT INTO public.tb_blocos_rotina OVERRIDING SYSTEM VALUE VALUES (631, 5, '13', 13, '-- REMOVE CARACTERES NÃO NUMÉRICOS NA MATRICULA
UPDATE 
	SCH_SGIC.AUX_USUARIO_OUTRAS_ENTIDADES
SET
	MATRICULA =  REGEXP_REPLACE(MATRICULA, ''[^0-9]'', '''', ''gi'');', 'UPDATE', '2026-03-25 15:24:39.190306-03');
INSERT INTO public.tb_blocos_rotina OVERRIDING SYSTEM VALUE VALUES (632, 5, '14', 14, '-- VERIFICA INEXISTENCIA DE 11 CARACTERES PARA A MATRÍCULA
SELECT 
	MIN(CHARACTER_LENGTH(MATRICULA))
FROM
	SCH_SGIC.AUX_USUARIO_OUTRAS_ENTIDADES;', 'SELECT', '2026-03-25 15:24:39.190699-03');
INSERT INTO public.tb_blocos_rotina OVERRIDING SYSTEM VALUE VALUES (633, 5, '15', 15, 'UPDATE SCH_SGIC.AUX_USUARIO_OUTRAS_ENTIDADES
   SET MATRICULA = LPAD(TRIM(MATRICULA),11,''0'')
 WHERE CHARACTER_LENGTH (TRIM(MATRICULA)) < 11;', 'UPDATE', '2026-03-25 15:24:39.191118-03');
INSERT INTO public.tb_blocos_rotina OVERRIDING SYSTEM VALUE VALUES (634, 5, '16', 16, '-- REMOVE CARACTERES NÃO NUMÉRICOS NA MATRICULA_CHEFIA
UPDATE SCH_SGIC.AUX_USUARIO_OUTRAS_ENTIDADES
 SET MATRICULA_CHEFIA =  REGEXP_REPLACE(MATRICULA_CHEFIA, ''[^0-9]'', '''', ''gi'');', 'UPDATE', '2026-03-25 15:24:39.191543-03');
INSERT INTO public.tb_blocos_rotina OVERRIDING SYSTEM VALUE VALUES (635, 5, '17', 17, '-- VERIFICA INEXISTENCIA DE 11 CARACTERES PARA A MATRÍCULA CHEFIA
SELECT MIN(CHARACTER_LENGTH(MATRICULA_CHEFIA))
  FROM SCH_SGIC.AUX_USUARIO_OUTRAS_ENTIDADES;', 'SELECT', '2026-03-25 15:24:39.191962-03');
INSERT INTO public.tb_blocos_rotina OVERRIDING SYSTEM VALUE VALUES (636, 5, '18', 18, 'UPDATE SCH_SGIC.AUX_USUARIO_OUTRAS_ENTIDADES
   SET MATRICULA_CHEFIA = LPAD(TRIM(MATRICULA_CHEFIA),11,''0'')
 WHERE CHARACTER_LENGTH (TRIM(MATRICULA_CHEFIA)) < 11;', 'UPDATE', '2026-03-25 15:24:39.19258-03');
INSERT INTO public.tb_blocos_rotina OVERRIDING SYSTEM VALUE VALUES (637, 5, '19', 19, 'INSERT INTO SCH_SGIC.SGIC_USUARIO
SELECT nextval(''sch_sgic.sgic_usuario_id_usuario_seq''::regclass),
       NULL data_alteracao,
       NULL data_exclusao,
       CURRENT_DATE data_inclusao,
       FALSE bloqueado,
       NULL cargo_efetivo_instrutor,
       NULL categoria_orgao_origem,
       X.CPF CPF,
       NULL curriculo_instrutor,
       ''2060-01-01 00:00:00'' data_validade,
       X.EMAIL email,
       TRUE ind_ativo,
       X.NOME nome,
       NULL placa_carro_instrutor,
       TRUE recebe_notif_email,
       NULL rg_instrutor,
       ''1000:5b4240353134306661:8a075af6298092071869f6cc1cb76f6e4e312805436874d08d80b8065c4784e63d0f4e77e66099f8cba1f4638a97990b6a90ff9bd14cd43b926e71eaa9aa86f9'' senha,
       X.TELEFONE telefone,
       NULL tele_residencial_instrutor,
       NULL tipo_usuario_instrutor,
       NULL id_chefe_imediato
  FROM SCH_SGIC.AUX_USUARIO_OUTRAS_ENTIDADES X
 WHERE NOT EXISTS (SELECT CPF
                     FROM SCH_SGIC.SGIC_USUARIO U
                   WHERE U.CPF = X.CPF) order by 8;', 'INSERT', '2026-03-25 15:24:39.193517-03');
INSERT INTO public.tb_blocos_rotina OVERRIDING SYSTEM VALUE VALUES (638, 5, '20', 20, '--No Update lembrar de excluir logicamente usuario_outras_entidades caso tenha alterado a entidade do vínculo atual
--Incluir o registro em usuario_outras_entidades do novo vínculo

--A-CPF não veio no arquivo de outras entidades
--B-Excluir o id_usuario na tabela usuario_outras_entidades
--C-Excluir logicamente apenas os que não estejam nas empresas e categorias citadas no WHERE.
SELECT *
  FROM sch_sgic.sgic_usuario u JOIN sch_sgic.sgic_usuario_outras_entidades uo
    ON U.id_usuario = uo.id_usuario
 WHERE UO.IND_ATIVO = TRUE
   AND U.IND_ATIVO = TRUE
   --VERIFICA SE O USUÁRIO ENCONTRA-SE NO ARQUIVO EXCEL(.CSV)
   AND NOT EXISTS (SELECT 1
                     FROM sch_sgic.aux_usuario_outras_entidades x
                    WHERE x.cpf = u.cpf
                      and lpad(x.matricula,11,''0'') = lpad(uo.matricula,11,''0''))
   --VERIFICA SE O USUARIO FAZ PARTE DO SADRH
   AND NOT EXISTS (SELECT *
                     FROM sch_sad.sad_servidor s JOIN sch_sad.sad_vinculo v
                       ON s.codigo = v.codigo_sad_servidor
                    WHERE s.cpf = u.cpf
                      AND s.ind_ativo = ''1''
                      AND v.ind_ativo = ''1''
                      AND v.codigo_srh_empresa in (''029'',''045'',''047'',''133'')
                      AND v.codigo_srh_categoria_funcional in (''APO'',''RF'',''RFB'',''RR'',''RRB''))
   --VERIFICA SE A EMPRESA ENCONTRA-SE NO ARQUIVO EXCEL(.CSV)
   AND EXISTS (SELECT distinct id_outras_entidades
                 FROM sch_sgic.aux_usuario_outras_entidades x2
                WHERE x2.id_outras_entidades = uo.id_outras_entidades);
', 'SELECT', '2026-03-25 15:24:39.194313-03');
INSERT INTO public.tb_blocos_rotina OVERRIDING SYSTEM VALUE VALUES (639, 5, '21', 21, 'UPDATE SCH_SGIC.SGIC_USUARIO_OUTRAS_ENTIDADES
   SET IND_ATIVO = FALSE
 WHERE IND_ATIVO = TRUE
   AND ID_USUARIO IN (SELECT U.ID_USUARIO
            FROM sch_sgic.sgic_usuario u JOIN sch_sgic.sgic_usuario_outras_entidades uo
                          ON U.id_usuario = uo.id_usuario
                       WHERE U.IND_ATIVO = TRUE
                         AND NOT EXISTS (SELECT 1
                                           FROM sch_sgic.aux_usuario_outras_entidades x
                                          WHERE x.cpf = u.cpf
                                            AND lpad(x.matricula,11,''0'') = lpad(uo.matricula,11,''0''))
       AND NOT EXISTS (SELECT *
                                           FROM sch_sad.sad_servidor s JOIN sch_sad.sad_vinculo v
                                             ON s.codigo = v.codigo_sad_servidor
                                          WHERE s.cpf = u.cpf
                                            AND s.ind_ativo = ''1''
                                            AND v.ind_ativo = ''1''
                                            AND v.codigo_srh_empresa in (''029'',''045'',''047'',''133'')
                                            AND v.codigo_srh_categoria_funcional in (''APO'',''RF'',''RFB'',''RR'',''RRB''))
                         AND EXISTS (SELECT distinct id_outras_entidades
                                       FROM sch_sgic.aux_usuario_outras_entidades x2
                                      WHERE x2.id_outras_entidades = uo.id_outras_entidades));', 'UPDATE', '2026-03-25 15:24:39.19487-03');
INSERT INTO public.tb_blocos_rotina OVERRIDING SYSTEM VALUE VALUES (608, 2, '26', 26, 'UPDATE SCH_SGIC.SGIC_USUARIO U set
    DATA_ALTERACAO = CURRENT_DATE,
  ID_CHEFE_IMEDIATO = UC.ID_USUARIO,
  MATRICULA_CHEFIA_IMEDIATA = LPAD(TRIM(UC.MATRICULA),11,''0'')
 FROM (SELECT DISTINCT C.CPF_FUNC,
                       U2.ID_USUARIO,
                       C.MATRICULA
         FROM SCH_SGIC.SGIC_USUARIO U2 JOIN (SELECT X.CPF AS CPF_FUNC,
                                              X2.CPF AS CPF_CHEFE,
                                              X2.MATRICULA
                                         FROM SCH_SGIC.AUX_USUARIO_OUTRAS_ENTIDADES X
                 JOIN SCH_SGIC.AUX_USUARIO_OUTRAS_ENTIDADES X2
                   ON X.MATRICULA_CHEFIA = X2.MATRICULA) C
     ON U2.CPF = C.CPF_CHEFE) UC
WHERE U.CPF = UC.CPF_FUNC;', 'UPDATE', '2026-03-25 15:24:24.607667-03');
INSERT INTO public.tb_blocos_rotina OVERRIDING SYSTEM VALUE VALUES (610, 2, '28', 28, '----------------------------------------------
-- Consulta inconsistência na coluna matricula
----------------------------------------------
select * -- count(*)
from sch_sgic.sgic_usuario_outras_entidades
where length(matricula) < 11
or matricula ~ ''[^0-9]''; -- caracter não numérico', 'SELECT', '2026-03-25 15:24:24.608519-03');
INSERT INTO public.tb_blocos_rotina OVERRIDING SYSTEM VALUE VALUES (611, 2, '29', 29, '-----------------------------------------------------
-- Remove caractres não numéricos na coluna matricula
-----------------------------------------------------
UPDATE SCH_SGIC.SGIC_USUARIO_OUTRAS_ENTIDADES
 SET MATRICULA =  REGEXP_REPLACE(MATRICULA, ''[^0-9]'', '''', ''gi'')
WHERE LENGTH(MATRICULA) < 11
OR MATRICULA ~ ''[^0-9]'';', 'UPDATE', '2026-03-25 15:24:24.609031-03');
INSERT INTO public.tb_blocos_rotina OVERRIDING SYSTEM VALUE VALUES (644, 5, '26', 26, 'UPDATE SCH_SGIC.SGIC_USUARIO U set
    DATA_ALTERACAO = CURRENT_DATE,
  ID_CHEFE_IMEDIATO = UC.ID_USUARIO,
  MATRICULA_CHEFIA_IMEDIATA = LPAD(TRIM(UC.MATRICULA),11,''0'')
 FROM (SELECT DISTINCT C.CPF_FUNC,
                       U2.ID_USUARIO,
                       C.MATRICULA
         FROM SCH_SGIC.SGIC_USUARIO U2 JOIN (SELECT X.CPF AS CPF_FUNC,
                                              X2.CPF AS CPF_CHEFE,
                                              X2.MATRICULA
                                         FROM SCH_SGIC.AUX_USUARIO_OUTRAS_ENTIDADES X
                 JOIN SCH_SGIC.AUX_USUARIO_OUTRAS_ENTIDADES X2
                   ON X.MATRICULA_CHEFIA = X2.MATRICULA) C
     ON U2.CPF = C.CPF_CHEFE) UC
WHERE U.CPF = UC.CPF_FUNC;', 'UPDATE', '2026-03-25 15:24:39.19732-03');
INSERT INTO public.tb_blocos_rotina OVERRIDING SYSTEM VALUE VALUES (645, 5, '27', 27, '------------------------
--ATUALIZAÇÃO DO USUÁRIO
------------------------
UPDATE SCH_SGIC.SGIC_USUARIO U
   SET DATA_ALTERACAO = CURRENT_DATE,
       NOME = UPPER(X.NOME)
  FROM SCH_SGIC.AUX_USUARIO_OUTRAS_ENTIDADES X
WHERE U.CPF = X.CPF;', 'UPDATE', '2026-03-25 15:24:39.197973-03');
INSERT INTO public.tb_blocos_rotina OVERRIDING SYSTEM VALUE VALUES (646, 5, '28', 28, '----------------------------------------------
-- Consulta inconsistência na coluna matricula
----------------------------------------------
select * -- count(*)
from sch_sgic.sgic_usuario_outras_entidades
where length(matricula) < 11
or matricula ~ ''[^0-9]''; -- caracter não numérico', 'SELECT', '2026-03-25 15:24:39.198432-03');
INSERT INTO public.tb_blocos_rotina OVERRIDING SYSTEM VALUE VALUES (647, 5, '29', 29, '-----------------------------------------------------
-- Remove caractres não numéricos na coluna matricula
-----------------------------------------------------
UPDATE SCH_SGIC.SGIC_USUARIO_OUTRAS_ENTIDADES
 SET MATRICULA =  REGEXP_REPLACE(MATRICULA, ''[^0-9]'', '''', ''gi'')
WHERE LENGTH(MATRICULA) < 11
OR MATRICULA ~ ''[^0-9]'';', 'UPDATE', '2026-03-25 15:24:39.198918-03');
INSERT INTO public.tb_blocos_rotina OVERRIDING SYSTEM VALUE VALUES (648, 5, '30', 30, '-------------------------------------------------
-- Exclusão de registros com a matrícula repetida
-------------------------------------------------
DELETE FROM SCH_SGIC.SGIC_USUARIO_OUTRAS_ENTIDADES T1
USING SCH_SGIC.SGIC_USUARIO_OUTRAS_ENTIDADES T2
WHERE LENGTH(T2.MATRICULA) < 11
AND T1.ID_USUARIO = T2.ID_USUARIO
AND T1.ID_OUTRAS_ENTIDADES = T2.ID_OUTRAS_ENTIDADES
AND T1.MATRICULA = LPAD(TRIM(T2.MATRICULA),11,''0'');', 'DELETE', '2026-03-25 15:24:39.199388-03');
INSERT INTO public.tb_blocos_rotina OVERRIDING SYSTEM VALUE VALUES (650, 5, '32', 32, '--------------------------------------------------------------------------------------------
-- Consulta a quantidade de usuários outras entidades na tabela sch_sgic.sgic_usuario_escola
--------------------------------------------------------------------------------------------
select count(ue.id_usuario)
from sch_sgic.sgic_usuario_escola ue
join sch_sgic.sgic_usuario_outras_entidades uoe on ue.id_usuario = uoe.id_usuario
where ue.id_escola in (
  select e.id_escola
  from sch_sgic.sgic_escola e
  where e.ind_atende_todas_instituicoes = true
);', 'SELECT', '2026-03-25 15:24:39.200331-03');
INSERT INTO public.tb_blocos_rotina OVERRIDING SYSTEM VALUE VALUES (651, 5, '33', 33, '--------------------------------------------------------------------------------
-- Exclusão dos usuários outras entidades na tabela sch_sgic.sgic_usuario_escola
--------------------------------------------------------------------------------
delete from sch_sgic.sgic_usuario_escola ue
using sch_sgic.sgic_usuario_outras_entidades uoe
where ue.id_usuario = uoe.id_usuario
and ue.id_escola in (
  select e.id_escola
  from sch_sgic.sgic_escola e
  where e.ind_atende_todas_instituicoes = true
);', 'DELETE', '2026-03-25 15:24:39.200824-03');
INSERT INTO public.tb_blocos_rotina OVERRIDING SYSTEM VALUE VALUES (652, 5, '34', 34, '--------------------------------------------------------------------------------
-- Inclusão dos usuários outras entidades na tabela sch_sgic.sgic_usuario_escola
--------------------------------------------------------------------------------
insert into sch_sgic.sgic_usuario_escola (id_usuario, ind_ativo, matricula, codigo_instituicao, id_escola, id_outras_entidades)
select u.id_usuario
      , CASE WHEN uoe.ind_ativo = ''1'' THEN true else false end ind_ativo
      , LPAD( uoe.matricula, 11, ''0'')
      , 0 codigo_instituicao
      , e.id_escola
      , uoe.id_outras_entidades
from sch_sgic.sgic_usuario_outras_entidades uoe
join sch_sgic.sgic_usuario u on uoe.id_usuario = u.id_usuario
join sch_sgic.sgic_escola e on e.ind_atende_todas_instituicoes = true
where uoe.ind_ativo = ''1'';', 'INSERT', '2026-03-25 15:24:39.201293-03');
INSERT INTO public.tb_blocos_rotina OVERRIDING SYSTEM VALUE VALUES (653, 5, '35', 35, 'DROP TABLE IF EXISTS SCH_SGIC.AUX_USUARIO_OUTRAS_ENTIDADES;', 'DROP', '2026-03-25 15:24:39.201771-03');
INSERT INTO public.tb_blocos_rotina OVERRIDING SYSTEM VALUE VALUES (654, 5, '36', 36, 'DROP TABLE IF EXISTS sch_sgic.bkp_sgic_outras_entidades;', 'DROP', '2026-03-25 15:24:39.202239-03');
INSERT INTO public.tb_blocos_rotina OVERRIDING SYSTEM VALUE VALUES (533, 1, '1', 1, '-- CRIACAO DE TABELA DE LOG DE CARGA SGIC. CRIAR APENAS SE NÃO EXISTIR.
CREATE TABLE IF NOT EXISTS sch_sgic.log_carga_sgic
(
  id serial4 primary key,
  numero_script CHAR(3),
  momento_execucao TIMESTAMP,
  descricao CHARACTER VARYING (100)
)
WITH (
  OIDS=FALSE
);', 'CREATE', '2026-03-25 15:23:59.664375-03');
INSERT INTO public.tb_blocos_rotina OVERRIDING SYSTEM VALUE VALUES (534, 1, '2', 2, 'ALTER TABLE sch_sgic.log_carga_sgic OWNER TO saduseradm;', 'ALTER', '2026-03-25 15:23:59.666007-03');
INSERT INTO public.tb_blocos_rotina OVERRIDING SYSTEM VALUE VALUES (535, 1, '3', 3, 'GRANT ALL ON TABLE sch_sgic.log_carga_sgic TO saduseradm;', 'GRANT', '2026-03-25 15:23:59.667043-03');
INSERT INTO public.tb_blocos_rotina OVERRIDING SYSTEM VALUE VALUES (536, 1, '4', 4, 'GRANT ALL ON TABLE sch_sgic.log_carga_sgic TO sadsisweb;', 'SELECT', '2026-03-25 15:23:59.668013-03');
INSERT INTO public.tb_blocos_rotina OVERRIDING SYSTEM VALUE VALUES (537, 1, '5', 5, 'GRANT SELECT ON TABLE sch_sgic.log_carga_sgic TO saduserr;', 'GRANT', '2026-03-25 15:23:59.66892-03');
INSERT INTO public.tb_blocos_rotina OVERRIDING SYSTEM VALUE VALUES (538, 1, '6', 6, 'GRANT ALL ON ALL TABLES IN SCHEMA sch_sad TO saduseradm;', 'GRANT', '2026-03-25 15:23:59.66954-03');
INSERT INTO public.tb_blocos_rotina OVERRIDING SYSTEM VALUE VALUES (539, 1, '7', 7, 'GRANT SELECT, INSERT, UPDATE, DELETE ON ALL TABLES IN SCHEMA sch_sad TO sadsisweb;', 'GRANT', '2026-03-25 15:23:59.670214-03');
INSERT INTO public.tb_blocos_rotina OVERRIDING SYSTEM VALUE VALUES (540, 1, '8', 8, 'GRANT ALL ON ALL SEQUENCES IN SCHEMA sch_sad TO sadsisweb;', 'GRANT', '2026-03-25 15:23:59.670735-03');
INSERT INTO public.tb_blocos_rotina OVERRIDING SYSTEM VALUE VALUES (541, 1, '9', 9, 'SELECT 
	count(0)
FROM 
	sch_sgic.sgic_usuario u
WHERE 
	u.ind_ativo = TRUE
	AND EXISTS (
    SELECT 1
    FROM sch_sad.imp_servidor s
    WHERE s.cpf = u.cpf
     AND s.ind_ativo = ''0''
  )
	AND NOT EXISTS (
    SELECT 1
    FROM sch_sgic.sgic_usuario_outras_entidades uo
    WHERE u.id_usuario = uo.id_usuario
 );', 'SELECT', '2026-03-25 15:23:59.671261-03');
INSERT INTO public.tb_blocos_rotina OVERRIDING SYSTEM VALUE VALUES (542, 1, '10', 10, 'UPDATE 
	sch_sgic.sgic_usuario u
SET 
	ind_ativo = FALSE
	,data_alteracao = current_date
WHERE 
	u.ind_ativo = TRUE
	AND EXISTS (
    SELECT 1
    FROM sch_sad.imp_servidor s
    WHERE s.cpf = u.cpf
    AND s.ind_ativo = ''0''
 )
	AND NOT EXISTS (
    	SELECT 1
      FROM sch_sgic.sgic_usuario_outras_entidades uo
      WHERE u.id_usuario = uo.id_usuario
 );', 'UPDATE', '2026-03-25 15:23:59.671987-03');
INSERT INTO public.tb_blocos_rotina OVERRIDING SYSTEM VALUE VALUES (543, 1, '11', 11, 'INSERT INTO sch_sgic.sgic_usuario(
            data_inclusao,
            bloqueado,
            cargo_efetivo_instrutor,
            categoria_orgao_origem,
            cpf,
            curriculo_instrutor,
            data_validade,
            email,
            ind_ativo,
            nome,
            placa_carro_instrutor,
            recebe_notif_email,
            rg_instrutor,
            senha,
            telefone,
            tele_residencial_instrutor,
            tipo_usuario_instrutor)
SELECT DISTINCT current_date, -- data_inclusao
       FALSE, --bloqueado
       NULL, --cargo_efetivo
       NULL, --categoria_orgao_origem
       cpf,
       NULL, --curriculo
       CAST(''2060-01-01 00:00:00'' as timestamp), --data_validade,
       NULL,--v.email, --email
       CASE WHEN s.ind_ativo = ''0'' THEN FALSE ELSE TRUE END, --ind_ativo,
       nome, --nome
       NULL, --placa_carro
       CAST(TRUE as boolean), --recebe_notif_email
       NULL, --rg_instrutor
       ''CAMPO SENHA PARA USUARIOS DE OUTRAS ENTIDADES'', --senha
       NULL,--ddd_telefone || telefone, --telefone
       NULL, --tele_residencial
       NULL --tipo_usuario
FROM sch_sad.imp_servidor s JOIN sch_sad.imp_vinculo v
ON s.codigo = v.codigo_sad_servidor
WHERE not exists (SELECT cpf
                    FROM sch_sgic.sgic_usuario u
                    WHERE u.cpf = s.cpf)
AND s.ind_ativo = ''1''
AND v.ind_ativo = ''1''
AND v.codigo_srh_categoria_funcional IN (''AGP'',''CCB'',''CIV'',''CLT'',''COM'',''CTD'',''DES'',''ELE'',''EST'',
''EXQ'',''EXM'',''FRQ'',''GAP'',''MCB'',''MLT'',''REV'',''VOG'');', 'INSERT', '2026-03-25 15:23:59.67269-03');
INSERT INTO public.tb_blocos_rotina OVERRIDING SYSTEM VALUE VALUES (544, 1, '12', 12, 'SELECT 
	count(*)
FROM 
	sch_sad.imp_servidor s 
  JOIN sch_sad.imp_vinculo v 	ON s.codigo = v.codigo_sad_servidor
	JOIN sch_sgic.sgic_usuario u ON s.cpf = u.cpf
WHERE
	s.ind_ativo = ''1''
	AND v.ind_ativo = ''1''
	AND v.codigo_srh_categoria_funcional in (''AGP'',''CCB'',''CIV'',''CLT'',''COM'',''CTD'',''DES'',''ELE'',''EST'',''GAP'',''MCB'',''MLT'',''REV'',''VOG'')
	AND u.nome <> s.nome
;', 'SELECT', '2026-03-25 15:23:59.673404-03');
INSERT INTO public.tb_blocos_rotina OVERRIDING SYSTEM VALUE VALUES (545, 1, '13', 13, 'UPDATE 
	sch_sgic.sgic_usuario u
SET 
	nome = s.nome
FROM
	sch_sad.imp_servidor s JOIN sch_sad.imp_vinculo v ON s.codigo = v.codigo_sad_servidor
WHERE
	s.cpf = u.cpf
	AND s.ind_ativo = ''1''
	AND v.ind_ativo = ''1''
	AND v.codigo_srh_categoria_funcional in (''AGP'',''CCB'',''CIV'',''CLT'',''COM'',''CTD'',''DES'',''ELE'',''EST'',''GAP'',''MCB'',''MLT'',''REV'',''VOG'')
	AND u.nome <> s.nome
;', 'UPDATE', '2026-03-25 15:23:59.674108-03');
INSERT INTO public.tb_blocos_rotina OVERRIDING SYSTEM VALUE VALUES (546, 1, '14', 14, 'TRUNCATE TABLE SCH_SAD.SAD_INSTITUICAO CASCADE;', 'TRUNCATE', '2026-03-25 15:23:59.674729-03');
INSERT INTO public.tb_blocos_rotina OVERRIDING SYSTEM VALUE VALUES (547, 1, '15', 15, 'TRUNCATE TABLE SCH_SAD.SAD_VINCULO CASCADE;', 'TRUNCATE', '2026-03-25 15:23:59.675395-03');
INSERT INTO public.tb_blocos_rotina OVERRIDING SYSTEM VALUE VALUES (553, 1, '22', 22, 'SELECT SETVAL(''sch_sad.sad_instituicao_codigo_seq'' , MAX(CODIGO)) FROM sch_sad.sad_instituicao;', 'SELECT', '2026-03-25 15:23:59.681933-03');
INSERT INTO public.tb_blocos_rotina OVERRIDING SYSTEM VALUE VALUES (557, 1, '26', 26, 'SELECT sch_sad.fn_excluir_tab_imp();', 'SELECT', '2026-03-25 15:23:59.684975-03');
INSERT INTO public.tb_blocos_rotina OVERRIDING SYSTEM VALUE VALUES (556, 1, '25', 25, 'insert into sch_sgic.sgic_usuario_escola (id_usuario, ind_ativo, matricula, codigo_instituicao, id_escola, id_outras_entidades)
select distinct u.id_usuario
      , CASE WHEN v.ind_ativo = ''1'' THEN true else false end ind_ativo
      , LPAD(v.matricula, 11, ''0'')
      , v.codigo_sad_instituicao
      , e.id_escola
      , 0 id_outras_entidades
from 
	sch_sad.sad_servidor s
	join sch_sad.sad_vinculo v on s.codigo = v.codigo_sad_servidor
	join sch_sgic.sgic_usuario u on s.cpf = u.cpf
	join sch_sgic.sgic_escola e on e.ind_atende_todas_instituicoes = true
where
	s.ind_ativo = ''1'';', 'INSERT', '2026-03-25 15:23:59.683981-03');
INSERT INTO public.tb_blocos_rotina OVERRIDING SYSTEM VALUE VALUES (555, 1, '24', 24, 'delete from sch_sgic.sgic_usuario_escola
where id_usuario in (
  select u.id_usuario
  from sch_sad.sad_servidor s
  join sch_sad.sad_vinculo v on s.codigo = v.codigo_sad_servidor
  join sch_sgic.sgic_usuario u on s.cpf = u.cpf
  join sch_sgic.sgic_escola e on e.ind_atende_todas_instituicoes = true
  where s.ind_ativo = ''1''
);', 'SELECT', '2026-03-25 15:23:59.683162-03');
INSERT INTO public.tb_blocos_rotina OVERRIDING SYSTEM VALUE VALUES (554, 1, '23', 23, 'select 
	count(u.id_usuario)
from
	sch_sad.sad_servidor s
	join sch_sad.sad_vinculo v on s.codigo = v.codigo_sad_servidor
	join sch_sgic.sgic_usuario u on s.cpf = u.cpf
	join sch_sgic.sgic_escola e on e.ind_atende_todas_instituicoes = true
where 
	s.ind_ativo = ''1'';', 'SELECT', '2026-03-25 15:23:59.682525-03');
INSERT INTO public.tb_blocos_rotina OVERRIDING SYSTEM VALUE VALUES (552, 1, '21', 21, 'SELECT SETVAL(''sch_sad.sad_vinculo_codigo_seq'' , MAX(CODIGO)) FROM sch_sad.sad_vinculo;', 'SELECT', '2026-03-25 15:23:59.68132-03');
INSERT INTO public.tb_blocos_rotina OVERRIDING SYSTEM VALUE VALUES (551, 1, '20', 20, 'SELECT SETVAL(''sch_sad.sad_servidor_codigo_seq'', MAX(CODIGO)) FROM sch_sad.sad_servidor;', 'SELECT', '2026-03-25 15:23:59.680569-03');
INSERT INTO public.tb_blocos_rotina OVERRIDING SYSTEM VALUE VALUES (550, 1, '19', 19, 'INSERT INTO SCH_SAD.SAD_VINCULO
SELECT codigo, ind_ativo, matricula, codigo_sad_instituicao, nome_sad_instituicao,
  data_efetivo_exercicio, ind_lider, ind_vinculo_exercicio, codigo_srh_cargo,
  nome_sad_cargo, codigo_srh_cargo_origem, nome_cargo_origem, matricula_origem,
  codigo_instituicao_origem, codigo_srh_funcao, nome_sad_funcao, codigo_srh_cargo_comissionado,
  nome_cargo_comissionado, codigo_srh_funcao_gratificada, nome_funcao_gratificada, codigo_instituicao_chefia_imediata,
  matricula_chefe_sad_servidor, codigo_srh_estrut_orgn, nome_sad_estrut_orgn, codigo_srh_categoria_funcional,
  descricao_sad_categoria_funcional, codigo_srh_situacao_funcional, descricao_sad_situacao_funcional, codigo_srh_tipo_vinculo,
  descricao_sad_tipo_vinculo, email, codigo_srh_afastamento, descricao_afastamento, codigo_srh_grupo_ocupacional, descricao_sad_grupo_ocupacional,
  data_hora_incl, data_hora_altr, usuario_incl, usuario_altr, codigo_sad_servidor, tab_sal_cargo, tab_sal_funcao,
  codigo_srh_grupo_ocupacional_origem, codigo_sad_empresa_estab, codigo_srh_empresa, codigo_srh_estabelecimento,
  ind_atualizacao_srh, codigo_empresa_estab_origem, emp_origem, ind_busca_origem, ind_preenchimento,
  matricula_origem_sadrh, matricula_chefe_sad_servidor_sadrh, codigo_srh_empresa_sadrh, codigo_srh_estabelecimento_sadrh,
  codigo_srh_categoria_funcional_sadrh, descricao_sad_categoria_funcional_sadrh, codigo_srh_tipo_vinculo_sadrh,
  descricao_sad_tipo_vinculo_sadrh, codigo_sad_servidor_sadrh, matricula_sadrh
FROM sch_sad.imp_vinculo;', 'INSERT', '2026-03-25 15:23:59.677346-03');
INSERT INTO public.tb_blocos_rotina OVERRIDING SYSTEM VALUE VALUES (549, 1, '18', 18, 'INSERT INTO SCH_SAD.SAD_SERVIDOR SELECT * FROM SCH_SAD.IMP_SERVIDOR;', 'INSERT', '2026-03-25 15:23:59.676694-03');
INSERT INTO public.tb_blocos_rotina OVERRIDING SYSTEM VALUE VALUES (548, 1, '17', 17, 'INSERT INTO SCH_SAD.SAD_INSTITUICAO SELECT * FROM SCH_SAD.IMP_INSTITUICAO;', 'INSERT', '2026-03-25 15:23:59.676015-03');
INSERT INTO public.tb_blocos_rotina OVERRIDING SYSTEM VALUE VALUES (655, 1, '16', 16, 'TRUNCATE TABLE SCH_SAD.SAD_SERVIDOR CASCADE;', 'TRUNCATE', '2026-03-26 12:06:35.195498-03');
INSERT INTO public.tb_blocos_rotina OVERRIDING SYSTEM VALUE VALUES (685, 6, '1', 1, '-------------------------
--CRIAÇÃO TABELA DE LOG-- | --> PARA BANCO DE DADOS POSTGRES VERSÃO 8.4.11
-------------------------
CREATE OR REPLACE FUNCTION sch_sad.fn_criar_log_carga() 
RETURNS void AS 
$$
BEGIN
    IF NOT EXISTS (
        SELECT 1
        FROM information_schema.tables 
        WHERE table_schema = ''sch_sad'' 
          AND table_name = ''log_carga_sgd''
    ) THEN
        EXECUTE ''
            CREATE TABLE sch_sad.log_carga_sgd
            (
              id serial4 primary key,
              numero_script CHAR(3),
              momento_execucao TIMESTAMP,
              descricao CHARACTER VARYING (100)
            )'';
    END IF;
END;
$$ 
LANGUAGE plpgsql;', 'CREATE', '2026-03-26 16:48:49.87091-03');
INSERT INTO public.tb_blocos_rotina OVERRIDING SYSTEM VALUE VALUES (686, 6, '', 2, '-------------------------
--CRIAÇÃO TABELA DE LOG-- | --> PARA BANCO DE DADOS POSTGRES VERSÃO 8.4.11
-------------------------
SELECT sch_sad.fn_criar_log_carga();', 'SELECT', '2026-03-26 16:48:49.872767-03');
INSERT INTO public.tb_blocos_rotina OVERRIDING SYSTEM VALUE VALUES (687, 6, '', 3, 'GRANT SELECT, UPDATE, INSERT, DELETE ON TABLE sch_sad.log_carga_sgd TO sadsisweb;', 'GRANT', '2026-03-26 16:48:49.873315-03');
INSERT INTO public.tb_blocos_rotina OVERRIDING SYSTEM VALUE VALUES (688, 6, '', 4, 'GRANT SELECT ON TABLE sch_sad.log_carga_sgd TO saduserr;', 'GRANT', '2026-03-26 16:48:49.873798-03');
INSERT INTO public.tb_blocos_rotina OVERRIDING SYSTEM VALUE VALUES (689, 6, '', 5, '----------------------------------------
--CONFERÊNCIA DE TOTAIS DE TABELAS AUX--
----------------------------------------
select count(*) from sch_sad.aux_sad_empresa_estab;', 'CREATE', '2026-03-26 16:48:49.874259-03');
INSERT INTO public.tb_blocos_rotina OVERRIDING SYSTEM VALUE VALUES (690, 6, '', 6, 'select count(*) from sch_sad.aux_sad_tab_sal_cargo;', 'SELECT', '2026-03-26 16:48:49.874722-03');
INSERT INTO public.tb_blocos_rotina OVERRIDING SYSTEM VALUE VALUES (691, 6, '', 7, 'select count(*) from sch_sad.aux_sad_grupo_ocupacional;', 'SELECT', '2026-03-26 16:48:49.875189-03');
INSERT INTO public.tb_blocos_rotina OVERRIDING SYSTEM VALUE VALUES (692, 6, '', 8, 'select count(*) from sch_sad.aux_sad_categoria_funcional;', 'SELECT', '2026-03-26 16:48:49.876355-03');
INSERT INTO public.tb_blocos_rotina OVERRIDING SYSTEM VALUE VALUES (693, 6, '', 9, 'select count(*) from sch_sad.aux_sad_cargo;', 'SELECT', '2026-03-26 16:48:49.876924-03');
INSERT INTO public.tb_blocos_rotina OVERRIDING SYSTEM VALUE VALUES (694, 6, '', 10, 'select count(*) from sch_sad.aux_sad_servidor;', 'SELECT', '2026-03-26 16:48:49.877379-03');
INSERT INTO public.tb_blocos_rotina OVERRIDING SYSTEM VALUE VALUES (695, 6, '', 11, 'select count(*) from sch_sad.aux_sad_vinculo;', 'SELECT', '2026-03-26 16:48:49.877807-03');
INSERT INTO public.tb_blocos_rotina OVERRIDING SYSTEM VALUE VALUES (696, 6, '', 12, '------------
-- PASSO 1-- -- formatar AUX
------------
CREATE OR REPLACE FUNCTION public.fn_aux_chartoint(columnconvert character varying)
 RETURNS integer
 LANGUAGE sql
 IMMUTABLE STRICT
AS $function$
SELECT CASE WHEN trim($1) SIMILAR TO ''[0-9]+''
    THEN CAST(trim($1) AS int4)
ELSE NULL END;
$function$
;', 'CREATE', '2026-03-26 16:48:49.878349-03');
INSERT INTO public.tb_blocos_rotina OVERRIDING SYSTEM VALUE VALUES (697, 6, '', 13, 'ALTER TABLE sch_sad.aux_sad_vinculo
ALTER COLUMN codigo_cargo TYPE int4 USING fn_aux_ChartoInt(codigo_cargo);', 'ALTER', '2026-03-26 16:48:49.878847-03');
INSERT INTO public.tb_blocos_rotina OVERRIDING SYSTEM VALUE VALUES (698, 6, '', 14, 'ALTER TABLE sch_sad.aux_sad_vinculo
ALTER COLUMN codigo_funcao TYPE int4 USING fn_aux_ChartoInt(codigo_funcao);', 'ALTER', '2026-03-26 16:48:49.879261-03');
INSERT INTO public.tb_blocos_rotina OVERRIDING SYSTEM VALUE VALUES (699, 6, '', 15, 'ALTER TABLE sch_sad.aux_sad_vinculo
ALTER COLUMN data_admissao TYPE date USING fn_aux_ChartoDate(data_admissao);', 'ALTER', '2026-03-26 16:48:49.879655-03');
INSERT INTO public.tb_blocos_rotina OVERRIDING SYSTEM VALUE VALUES (700, 6, '', 16, 'ALTER TABLE sch_sad.aux_sad_vinculo
ALTER COLUMN codigo_tipo_vinculo TYPE int4 USING fn_aux_ChartoInt(codigo_tipo_vinculo);', 'ALTER', '2026-03-26 16:48:49.880046-03');
INSERT INTO public.tb_blocos_rotina OVERRIDING SYSTEM VALUE VALUES (701, 6, '', 17, 'ALTER TABLE sch_sad.aux_sad_vinculo
ALTER COLUMN codigo_situacao_func TYPE int4 USING fn_aux_ChartoInt(codigo_situacao_func);', 'ALTER', '2026-03-26 16:48:49.880425-03');
INSERT INTO public.tb_blocos_rotina OVERRIDING SYSTEM VALUE VALUES (702, 6, '', 18, 'ALTER TABLE sch_sad.aux_sad_vinculo
ALTER COLUMN cargo_origem TYPE int4 USING fn_aux_ChartoInt(cargo_origem);', 'ALTER', '2026-03-26 16:48:49.880811-03');
INSERT INTO public.tb_blocos_rotina OVERRIDING SYSTEM VALUE VALUES (703, 6, '', 19, 'CREATE OR REPLACE FUNCTION sch_sad.fn_formata_aux()
 RETURNS void
 LANGUAGE plpgsql
AS $function$
DECLARE
BEGIN
	PERFORM sch_sad.fn_formata_empresa_estab();
	PERFORM sch_sad.fn_formata_grupo_ocupacional();
	PERFORM sch_sad.fn_formata_categoria_funcional();
	PERFORM sch_sad.fn_formata_cargo();

END;
$function$
;', 'CREATE', '2026-03-26 16:48:49.881214-03');
INSERT INTO public.tb_blocos_rotina OVERRIDING SYSTEM VALUE VALUES (704, 6, '', 20, 'SELECT sch_sad.fn_formata_aux();', 'SELECT', '2026-03-26 16:48:49.881612-03');
INSERT INTO public.tb_blocos_rotina OVERRIDING SYSTEM VALUE VALUES (575, 4, '19', 19, 'INSERT INTO SCH_SAD.SAD_VINCULO
SELECT codigo, ind_ativo, matricula, codigo_sad_instituicao, nome_sad_instituicao,
  data_efetivo_exercicio, ind_lider, ind_vinculo_exercicio, codigo_srh_cargo,
  nome_sad_cargo, codigo_srh_cargo_origem, nome_cargo_origem, matricula_origem,
  codigo_instituicao_origem, codigo_srh_funcao, nome_sad_funcao, codigo_srh_cargo_comissionado,
  nome_cargo_comissionado, codigo_srh_funcao_gratificada, nome_funcao_gratificada, codigo_instituicao_chefia_imediata,
  matricula_chefe_sad_servidor, codigo_srh_estrut_orgn, nome_sad_estrut_orgn, codigo_srh_categoria_funcional,
  descricao_sad_categoria_funcional, codigo_srh_situacao_funcional, descricao_sad_situacao_funcional, codigo_srh_tipo_vinculo,
  descricao_sad_tipo_vinculo, email, codigo_srh_afastamento, descricao_afastamento, codigo_srh_grupo_ocupacional, descricao_sad_grupo_ocupacional,
  data_hora_incl, data_hora_altr, usuario_incl, usuario_altr, codigo_sad_servidor, tab_sal_cargo, tab_sal_funcao,
  codigo_srh_grupo_ocupacional_origem, codigo_sad_empresa_estab, codigo_srh_empresa, codigo_srh_estabelecimento,
  ind_atualizacao_srh, codigo_empresa_estab_origem, emp_origem, ind_busca_origem, ind_preenchimento,
  matricula_origem_sadrh, matricula_chefe_sad_servidor_sadrh, codigo_srh_empresa_sadrh, codigo_srh_estabelecimento_sadrh,
  codigo_srh_categoria_funcional_sadrh, descricao_sad_categoria_funcional_sadrh, codigo_srh_tipo_vinculo_sadrh,
  descricao_sad_tipo_vinculo_sadrh, codigo_sad_servidor_sadrh, matricula_sadrh
FROM sch_sad.imp_vinculo;', 'INSERT', '2026-03-25 15:24:11.409037-03');
INSERT INTO public.tb_blocos_rotina OVERRIDING SYSTEM VALUE VALUES (657, 4, '16', 16, 'TRUNCATE TABLE SCH_SAD.SAD_SERVIDOR CASCADE;', 'TRUNCATE', '2026-03-26 13:04:58.799845-03');
INSERT INTO public.tb_blocos_rotina OVERRIDING SYSTEM VALUE VALUES (609, 2, '27', 27, '------------------------
--ATUALIZAÇÃO DO USUÁRIO
------------------------
UPDATE SCH_SGIC.SGIC_USUARIO U
   SET DATA_ALTERACAO = CURRENT_DATE,
       NOME = UPPER(X.NOME)
  FROM SCH_SGIC.AUX_USUARIO_OUTRAS_ENTIDADES X
WHERE U.CPF = X.CPF;', 'UPDATE', '2026-03-25 15:24:24.608093-03');
INSERT INTO public.tb_blocos_rotina OVERRIDING SYSTEM VALUE VALUES (649, 5, '31', 31, '-- Tratar o valor da coluna matrícula com 11 caracteres
UPDATE SCH_SGIC.SGIC_USUARIO_OUTRAS_ENTIDADES
 SET MATRICULA = LPAD(TRIM(MATRICULA),11,''0'')
WHERE LENGTH(MATRICULA) < 11;', 'UPDATE', '2026-03-25 15:24:39.199855-03');
INSERT INTO public.tb_blocos_rotina OVERRIDING SYSTEM VALUE VALUES (705, 6, '', 21, '------------
-- PASSO 2-- 
------------
--Exclusão, Inclusão e Atualização Tabelas Auxiliares
--VERIFICAÇÃO DE INTEGRIDADE ENTRE AS TABELAS SAD_EMPRESA_ESTAB E SAD_INSTITUICAO
SELECT COUNT(0) 
FROM SCH_SAD.SAD_EMPRESA_ESTAB
WHERE IND_ATIVO = ''t'' 
AND CODIGO_SAD_INSTITUICAO NOT IN 
(SELECT DISTINCT CODIGO 
FROM SCH_SAD.SAD_INSTITUICAO);', 'SELECT', '2026-03-26 16:48:49.882003-03');
INSERT INTO public.tb_blocos_rotina OVERRIDING SYSTEM VALUE VALUES (706, 6, '', 22, '-- Script 002
-- VERIFICAÇÃO SE HÁ ALGUM REGISTRO ATIVO NA TABELA SAD_INSTITUICAO NÃO EXISTENTE NA TABELA SAD_EMPRESA_ESTAB
-- CODIGO = 140 REFERE-SE A DETELPE(FUNAFIN), CUJO O CODIGO_SAD_INSTITUICAO NÃO É 66, DEVIDO AO CONTRACHEQUE.
-- ?????????????VERIFICAR COM ANDREA O CASO DA FUNAFIN - ARPE
-- ROWS: 0

SELECT CODIGO FROM SCH_SAD.SAD_INSTITUICAO I
WHERE ATIVO = ''t''
AND NOT EXISTS 
(SELECT 1 FROM SCH_SAD.SAD_EMPRESA_ESTAB E 
WHERE I.CODIGO_SRH_EMPRESA = E.CODIGO_SRH_EMPRESA 
AND I.CODIGO_SRH_ESTABELECIMENTO = E.CODIGO_SRH_ESTABELECIMENTO)
AND CODIGO NOT IN (66,140)
ORDER BY CODIGO_SRH_EMPRESA, CODIGO_SRH_ESTABELECIMENTO;', 'SELECT', '2026-03-26 16:48:49.882411-03');
INSERT INTO public.tb_blocos_rotina OVERRIDING SYSTEM VALUE VALUES (707, 6, '', 23, '--- Script 003
-- CONTINUAR DAQUI E REFAZER O SELECT ACIMA COLOCANDO COMO CHAVE O CODIGO_SRH_EMPRESA 
-- E CODIGO_SRH_ESTABELECIMENTO, UTILIZAR O NOT EXISTS.
UPDATE 
	SCH_SAD.SAD_INSTITUICAO
SET 
		ATIVO = FALSE
WHERE 
	CODIGO in (
		SELECT
			CODIGO 
		FROM 
			SCH_SAD.SAD_INSTITUICAO I
		WHERE 
			ATIVO = ''t''
			AND NOT EXISTS ( 
				SELECT 
					1 
				FROM 
					SCH_SAD.SAD_EMPRESA_ESTAB E 
				WHERE 
					I.CODIGO_SRH_EMPRESA = E.CODIGO_SRH_EMPRESA 
					AND I.CODIGO_SRH_ESTABELECIMENTO = E.CODIGO_SRH_ESTABELECIMENTO
			)
			AND CODIGO NOT IN (66,140)
			ORDER BY 
				CODIGO_SRH_EMPRESA
				,CODIGO_SRH_ESTABELECIMENTO
);', 'UPDATE', '2026-03-26 16:48:49.88284-03');
INSERT INTO public.tb_blocos_rotina OVERRIDING SYSTEM VALUE VALUES (708, 6, '', 24, '-- Script 004
-- VALIDAÇÃO, EXCLUSÃO LÓGICA, INCLUSÃO E ATUALIZAÇÃO DA TABELA SAD_GRUPO_OCUPACIONAL 
-- PELA TABELA AUX_SAD_GRUPO_OCUPACIONAL
-- VALIDAÇÃO NA TABELA SAD_GRUPO_OCUPACIONAL
-- ROWS: 0
SELECT COUNT(0) 
FROM SCH_SAD.SAD_GRUPO_OCUPACIONAL G
WHERE NOT EXISTS (SELECT CODIGO_SRH_GRUPO_OCUPACIONAL FROM SCH_SAD.AUX_SAD_GRUPO_OCUPACIONAL A 
                    WHERE G.CODIGO_SRH_GRUPO_OCUPACIONAL = A.CODIGO_SRH_GRUPO_OCUPACIONAL)
                    AND G.IND_ATIVO = ''t'' 
                    AND G.IND_ATUALIZACAO_SRH = ''1'';', 'SELECT', '2026-03-26 16:48:49.883548-03');
INSERT INTO public.tb_blocos_rotina OVERRIDING SYSTEM VALUE VALUES (709, 6, '', 25, '-- Script 005
-- EXCLUSÃO LÓGICA NA TABELA SAD_GRUPO_OCUPACIONAL
-- ROWS: 0
UPDATE SCH_SAD.SAD_GRUPO_OCUPACIONAL G
SET IND_ATIVO = ''f''
WHERE G.IND_ATIVO = ''t'' 
AND G.IND_ATUALIZACAO_SRH = ''1''
AND NOT EXISTS (SELECT CODIGO_SRH_GRUPO_OCUPACIONAL FROM SCH_SAD.AUX_SAD_GRUPO_OCUPACIONAL A 
                WHERE G.CODIGO_SRH_GRUPO_OCUPACIONAL = A.CODIGO_SRH_GRUPO_OCUPACIONAL);', 'UPDATE', '2026-03-26 16:48:49.884032-03');
INSERT INTO public.tb_blocos_rotina OVERRIDING SYSTEM VALUE VALUES (578, 4, '22', 22, 'SELECT SETVAL(''sch_sad.sad_instituicao_codigo_seq'' , MAX(CODIGO)) FROM sch_sad.sad_instituicao;', 'SELECT', '2026-03-25 15:24:11.412125-03');
INSERT INTO public.tb_blocos_rotina OVERRIDING SYSTEM VALUE VALUES (577, 4, '21', 21, 'SELECT SETVAL(''sch_sad.sad_vinculo_codigo_seq'' , MAX(CODIGO)) FROM sch_sad.sad_vinculo;', 'SELECT', '2026-03-25 15:24:11.410998-03');
INSERT INTO public.tb_blocos_rotina OVERRIDING SYSTEM VALUE VALUES (558, 4, '1', 1, '-- CRIACAO DE TABELA DE LOG DE CARGA SGIC. CRIAR APENAS SE NÃO EXISTIR.
CREATE TABLE IF NOT EXISTS sch_sgic.log_carga_sgic
(
  id serial4 primary key,
  numero_script CHAR(3),
  momento_execucao TIMESTAMP,
  descricao CHARACTER VARYING (100)
)
WITH (
  OIDS=FALSE
);', 'CREATE', '2026-03-25 15:24:11.392525-03');
INSERT INTO public.tb_blocos_rotina OVERRIDING SYSTEM VALUE VALUES (559, 4, '2', 2, 'ALTER TABLE sch_sgic.log_carga_sgic OWNER TO saduseradm;', 'ALTER', '2026-03-25 15:24:11.394432-03');
INSERT INTO public.tb_blocos_rotina OVERRIDING SYSTEM VALUE VALUES (560, 4, '3', 3, 'GRANT ALL ON TABLE sch_sgic.log_carga_sgic TO saduseradm;', 'GRANT', '2026-03-25 15:24:11.395441-03');
INSERT INTO public.tb_blocos_rotina OVERRIDING SYSTEM VALUE VALUES (561, 4, '4', 4, 'GRANT ALL ON TABLE sch_sgic.log_carga_sgic TO sadsisweb;', 'SELECT', '2026-03-25 15:24:11.396893-03');
INSERT INTO public.tb_blocos_rotina OVERRIDING SYSTEM VALUE VALUES (562, 4, '5', 5, 'GRANT SELECT ON TABLE sch_sgic.log_carga_sgic TO saduserr;', 'GRANT', '2026-03-25 15:24:11.398275-03');
INSERT INTO public.tb_blocos_rotina OVERRIDING SYSTEM VALUE VALUES (563, 4, '6', 6, 'GRANT ALL ON ALL TABLES IN SCHEMA sch_sad TO saduseradm;', 'GRANT', '2026-03-25 15:24:11.39945-03');
INSERT INTO public.tb_blocos_rotina OVERRIDING SYSTEM VALUE VALUES (564, 4, '7', 7, 'GRANT SELECT, INSERT, UPDATE, DELETE ON ALL TABLES IN SCHEMA sch_sad TO sadsisweb;', 'GRANT', '2026-03-25 15:24:11.400518-03');
INSERT INTO public.tb_blocos_rotina OVERRIDING SYSTEM VALUE VALUES (565, 4, '8', 8, 'GRANT ALL ON ALL SEQUENCES IN SCHEMA sch_sad TO sadsisweb;', 'GRANT', '2026-03-25 15:24:11.401409-03');
INSERT INTO public.tb_blocos_rotina OVERRIDING SYSTEM VALUE VALUES (566, 4, '9', 9, 'SELECT 
	count(0)
FROM 
	sch_sgic.sgic_usuario u
WHERE 
	u.ind_ativo = TRUE
	AND EXISTS (
    SELECT 1
    FROM sch_sad.imp_servidor s
    WHERE s.cpf = u.cpf
     AND s.ind_ativo = ''0''
  )
	AND NOT EXISTS (
    SELECT 1
    FROM sch_sgic.sgic_usuario_outras_entidades uo
    WHERE u.id_usuario = uo.id_usuario
 );', 'SELECT', '2026-03-25 15:24:11.402174-03');
INSERT INTO public.tb_blocos_rotina OVERRIDING SYSTEM VALUE VALUES (567, 4, '10', 10, 'UPDATE 
	sch_sgic.sgic_usuario u
SET 
	ind_ativo = FALSE
	,data_alteracao = current_date
WHERE 
	u.ind_ativo = TRUE
	AND EXISTS (
    SELECT 1
    FROM sch_sad.imp_servidor s
    WHERE s.cpf = u.cpf
    AND s.ind_ativo = ''0''
 )
	AND NOT EXISTS (
    	SELECT 1
      FROM sch_sgic.sgic_usuario_outras_entidades uo
      WHERE u.id_usuario = uo.id_usuario
 );', 'UPDATE', '2026-03-25 15:24:11.402908-03');
INSERT INTO public.tb_blocos_rotina OVERRIDING SYSTEM VALUE VALUES (568, 4, '11', 11, 'INSERT INTO sch_sgic.sgic_usuario(
            data_inclusao,
            bloqueado,
            cargo_efetivo_instrutor,
            categoria_orgao_origem,
            cpf,
            curriculo_instrutor,
            data_validade,
            email,
            ind_ativo,
            nome,
            placa_carro_instrutor,
            recebe_notif_email,
            rg_instrutor,
            senha,
            telefone,
            tele_residencial_instrutor,
            tipo_usuario_instrutor)
SELECT DISTINCT current_date, -- data_inclusao
       FALSE, --bloqueado
       NULL, --cargo_efetivo
       NULL, --categoria_orgao_origem
       cpf,
       NULL, --curriculo
       CAST(''2060-01-01 00:00:00'' as timestamp), --data_validade,
       NULL,--v.email, --email
       CASE WHEN s.ind_ativo = ''0'' THEN FALSE ELSE TRUE END, --ind_ativo,
       nome, --nome
       NULL, --placa_carro
       CAST(TRUE as boolean), --recebe_notif_email
       NULL, --rg_instrutor
       ''CAMPO SENHA PARA USUARIOS DE OUTRAS ENTIDADES'', --senha
       NULL,--ddd_telefone || telefone, --telefone
       NULL, --tele_residencial
       NULL --tipo_usuario
FROM sch_sad.imp_servidor s JOIN sch_sad.imp_vinculo v
ON s.codigo = v.codigo_sad_servidor
WHERE not exists (SELECT cpf
                    FROM sch_sgic.sgic_usuario u
                    WHERE u.cpf = s.cpf)
AND s.ind_ativo = ''1''
AND v.ind_ativo = ''1''
AND v.codigo_srh_categoria_funcional IN (''AGP'',''CCB'',''CIV'',''CLT'',''COM'',''CTD'',''DES'',''ELE'',''EST'',
''EXQ'',''EXM'',''FRQ'',''GAP'',''MCB'',''MLT'',''REV'',''VOG'');', 'INSERT', '2026-03-25 15:24:11.403638-03');
INSERT INTO public.tb_blocos_rotina OVERRIDING SYSTEM VALUE VALUES (569, 4, '12', 12, 'SELECT 
	count(*)
FROM 
	sch_sad.imp_servidor s 
  JOIN sch_sad.imp_vinculo v 	ON s.codigo = v.codigo_sad_servidor
	JOIN sch_sgic.sgic_usuario u ON s.cpf = u.cpf
WHERE
	s.ind_ativo = ''1''
	AND v.ind_ativo = ''1''
	AND v.codigo_srh_categoria_funcional in (''AGP'',''CCB'',''CIV'',''CLT'',''COM'',''CTD'',''DES'',''ELE'',''EST'',''GAP'',''MCB'',''MLT'',''REV'',''VOG'')
	AND u.nome <> s.nome
;', 'SELECT', '2026-03-25 15:24:11.40448-03');
INSERT INTO public.tb_blocos_rotina OVERRIDING SYSTEM VALUE VALUES (570, 4, '13', 13, 'UPDATE 
	sch_sgic.sgic_usuario u
SET 
	nome = s.nome
FROM
	sch_sad.imp_servidor s JOIN sch_sad.imp_vinculo v ON s.codigo = v.codigo_sad_servidor
WHERE
	s.cpf = u.cpf
	AND s.ind_ativo = ''1''
	AND v.ind_ativo = ''1''
	AND v.codigo_srh_categoria_funcional in (''AGP'',''CCB'',''CIV'',''CLT'',''COM'',''CTD'',''DES'',''ELE'',''EST'',''GAP'',''MCB'',''MLT'',''REV'',''VOG'')
	AND u.nome <> s.nome
;', 'UPDATE', '2026-03-25 15:24:11.405435-03');
INSERT INTO public.tb_blocos_rotina OVERRIDING SYSTEM VALUE VALUES (571, 4, '14', 14, 'TRUNCATE TABLE SCH_SAD.SAD_INSTITUICAO CASCADE;', 'TRUNCATE', '2026-03-25 15:24:11.406193-03');
INSERT INTO public.tb_blocos_rotina OVERRIDING SYSTEM VALUE VALUES (572, 4, '15', 15, 'TRUNCATE TABLE SCH_SAD.SAD_VINCULO CASCADE;', 'TRUNCATE', '2026-03-25 15:24:11.406923-03');
INSERT INTO public.tb_blocos_rotina OVERRIDING SYSTEM VALUE VALUES (576, 4, '20', 20, 'SELECT SETVAL(''sch_sad.sad_servidor_codigo_seq'', MAX(CODIGO)) FROM sch_sad.sad_servidor;', 'SELECT', '2026-03-25 15:24:11.410031-03');
INSERT INTO public.tb_blocos_rotina OVERRIDING SYSTEM VALUE VALUES (574, 4, '18', 18, 'INSERT INTO SCH_SAD.SAD_SERVIDOR SELECT * FROM SCH_SAD.IMP_SERVIDOR;', 'INSERT', '2026-03-25 15:24:11.40835-03');
INSERT INTO public.tb_blocos_rotina OVERRIDING SYSTEM VALUE VALUES (573, 4, '17', 17, 'INSERT INTO SCH_SAD.SAD_INSTITUICAO SELECT * FROM SCH_SAD.IMP_INSTITUICAO;', 'INSERT', '2026-03-25 15:24:11.407646-03');
INSERT INTO public.tb_blocos_rotina OVERRIDING SYSTEM VALUE VALUES (582, 4, '26', 26, 'SELECT sch_sad.fn_excluir_tab_imp();', 'SELECT', '2026-03-25 15:24:11.415827-03');
INSERT INTO public.tb_blocos_rotina OVERRIDING SYSTEM VALUE VALUES (581, 4, '25', 25, 'insert into sch_sgic.sgic_usuario_escola (id_usuario, ind_ativo, matricula, codigo_instituicao, id_escola, id_outras_entidades)
select distinct u.id_usuario
      , CASE WHEN v.ind_ativo = ''1'' THEN true else false end ind_ativo
      , LPAD(v.matricula, 11, ''0'')
      , v.codigo_sad_instituicao
      , e.id_escola
      , 0 id_outras_entidades
from 
	sch_sad.sad_servidor s
	join sch_sad.sad_vinculo v on s.codigo = v.codigo_sad_servidor
	join sch_sgic.sgic_usuario u on s.cpf = u.cpf
	join sch_sgic.sgic_escola e on e.ind_atende_todas_instituicoes = true
where
	s.ind_ativo = ''1'';', 'INSERT', '2026-03-25 15:24:11.415027-03');
INSERT INTO public.tb_blocos_rotina OVERRIDING SYSTEM VALUE VALUES (580, 4, '24', 24, 'delete from sch_sgic.sgic_usuario_escola
where id_usuario in (
  select u.id_usuario
  from sch_sad.sad_servidor s
  join sch_sad.sad_vinculo v on s.codigo = v.codigo_sad_servidor
  join sch_sgic.sgic_usuario u on s.cpf = u.cpf
  join sch_sgic.sgic_escola e on e.ind_atende_todas_instituicoes = true
  where s.ind_ativo = ''1''
);', 'SELECT', '2026-03-25 15:24:11.414203-03');
INSERT INTO public.tb_blocos_rotina OVERRIDING SYSTEM VALUE VALUES (579, 4, '23', 23, 'select 
	count(u.id_usuario)
from
	sch_sad.sad_servidor s
	join sch_sad.sad_vinculo v on s.codigo = v.codigo_sad_servidor
	join sch_sgic.sgic_usuario u on s.cpf = u.cpf
	join sch_sgic.sgic_escola e on e.ind_atende_todas_instituicoes = true
where 
	s.ind_ativo = ''1'';', 'SELECT', '2026-03-25 15:24:11.413209-03');
INSERT INTO public.tb_blocos_rotina OVERRIDING SYSTEM VALUE VALUES (640, 5, '22', 22, 'INSERT INTO sch_sgic.sgic_usuario_outras_entidades(
            id_usuario, ind_ativo, matricula, id_outras_entidades)
SELECT u.id_usuario,
       true,
       LPAD(TRIM(a.matricula),11,''0'') matricula,
       a.id_outras_entidades
  from sch_sgic.sgic_usuario u join sch_sgic.aux_usuario_outras_entidades a
    on u.cpf = a.cpf
 where not exists (select 1
                       from sch_sgic.sgic_usuario_outras_entidades uo
                      where uo.id_usuario = u.id_usuario
                        and LPAD(a.matricula,11,''0'') = LPAD(uo.matricula,11,''0'')
                        and a.id_outras_entidades = uo.id_outras_entidades)
     AND NOT EXISTS (SELECT 1
                     FROM sch_sad.sad_servidor s JOIN sch_sad.sad_vinculo v
                       ON s.codigo = v.codigo_sad_servidor
                    WHERE s.cpf = u.cpf
                      AND s.ind_ativo = ''1''
                      AND v.ind_ativo = ''1''
                      AND v.codigo_srh_empresa in (''029'',''045'',''047'',''133'')
                      AND v.codigo_srh_categoria_funcional in (''APO'',''RF'',''RFB'',''RR'',''RRB''));', 'INSERT', '2026-03-25 15:24:39.195376-03');
INSERT INTO public.tb_blocos_rotina OVERRIDING SYSTEM VALUE VALUES (641, 5, '23', 23, '-------------------------------------------------------------
--REATIVANDO USUÁRIOS EXISTENTES NA BASE QUE ESTÃO NO ARQUIVO
-------------------------------------------------------------
SELECT CPF,
       NOME
  FROM SCH_SGIC.SGIC_USUARIO U
 WHERE U.IND_ATIVO = FALSE
   AND EXISTS (SELECT X.CPF
                 FROM SCH_SGIC.AUX_USUARIO_OUTRAS_ENTIDADES X
                WHERE U.CPF = X.CPF);', 'SELECT', '2026-03-25 15:24:39.195899-03');
INSERT INTO public.tb_blocos_rotina OVERRIDING SYSTEM VALUE VALUES (642, 5, '24', 24, 'UPDATE SCH_SGIC.SGIC_USUARIO U
   SET IND_ATIVO = TRUE
WHERE U.IND_ATIVO = FALSE
  AND EXISTS (SELECT X.CPF
               FROM SCH_SGIC.AUX_USUARIO_OUTRAS_ENTIDADES X
              WHERE U.CPF = X.CPF);', 'UPDATE', '2026-03-25 15:24:39.196365-03');
INSERT INTO public.tb_blocos_rotina OVERRIDING SYSTEM VALUE VALUES (643, 5, '25', 25, '------------------------------
--ATUALIZANDO O CHEFE IMEDIATO
------------------------------
SELECT *
  FROM SCH_SGIC.SGIC_USUARIO U JOIN (SELECT DISTINCT C.CPF_FUNC,
U2.ID_USUARIO
FROM SCH_SGIC.SGIC_USUARIO U2 JOIN (SELECT X.CPF AS CPF_FUNC,
                                X2.CPF AS CPF_CHEFE
                            FROM SCH_SGIC.AUX_USUARIO_OUTRAS_ENTIDADES X
            JOIN SCH_SGIC.AUX_USUARIO_OUTRAS_ENTIDADES X2
            ON X.MATRICULA_CHEFIA = X2.MATRICULA) C
ON U2.CPF = C.CPF_CHEFE) UC
ON U.CPF = UC.CPF_FUNC;', 'SELECT', '2026-03-25 15:24:39.196836-03');
INSERT INTO public.tb_blocos_rotina OVERRIDING SYSTEM VALUE VALUES (583, 2, '1', 1, 'DROP TABLE IF EXISTS SCH_SGIC.AUX_USUARIO_OUTRAS_ENTIDADES;', 'DROP', '2026-03-25 15:24:24.590731-03');
INSERT INTO public.tb_blocos_rotina OVERRIDING SYSTEM VALUE VALUES (584, 2, '2', 2, 'CREATE TABLE IF NOT EXISTS SCH_SGIC.AUX_USUARIO_OUTRAS_ENTIDADES
(
  id bigserial,
  ID_OUTRAS_ENTIDADES integer NOT NULL,
  CPF character varying(11),
  MATRICULA character varying(11),
  EMAIL character varying(50),
  NOME character varying(100),
  TELEFONE character varying(20),
  MATRICULA_CHEFIA character varying(11),
  EMAIL_CHEFIA character varying(50)
)
WITH (
  OIDS=FALSE
);', 'CREATE', '2026-03-25 15:24:24.592116-03');
INSERT INTO public.tb_blocos_rotina OVERRIDING SYSTEM VALUE VALUES (585, 2, '3', 3, '--Caso retorne algum erro informando que esteja faltando o ID_OUTRAS_ENTIDADES, deve ser realizado o backup da tabela sch_sgic.sgic_outras_entidades do ambiente de produção e restaurar em homologação.
DROP TABLE IF EXISTS sch_sgic.bkp_sgic_outras_entidades;', 'DROP', '2026-03-25 15:24:24.593049-03');
INSERT INTO public.tb_blocos_rotina OVERRIDING SYSTEM VALUE VALUES (586, 2, '4', 4, 'CREATE TABLE IF NOT EXISTS sch_sgic.bkp_sgic_outras_entidades AS SELECT * FROM sch_sgic.sgic_outras_entidades;', 'CREATE', '2026-03-25 15:24:24.594137-03');
INSERT INTO public.tb_blocos_rotina OVERRIDING SYSTEM VALUE VALUES (587, 2, '5', 5, 'INSERT INTO sch_sgic.sgic_outras_entidades
SELECT id_outras_entidades,
       data_alteracao,
       data_exclusao,
       data_inclusao,
       ativo,
       esfera,
       descricao,
       poder,
       sigla,
       tipo,
       ind_prefeitura
  FROM sch_sgic.bkp_sgic_outras_entidades B
  WHERE NOT EXISTS (SELECT 1
      FROM SCH_SGIC.SGIC_OUTRAS_ENTIDADES O
     WHERE O.ID_OUTRAS_ENTIDADES = B.ID_OUTRAS_ENTIDADES);', 'INSERT', '2026-03-25 15:24:24.594959-03');
INSERT INTO public.tb_blocos_rotina OVERRIDING SYSTEM VALUE VALUES (588, 2, '6', 6, 'SELECT COUNT(*) FROM SCH_SGIC.AUX_USUARIO_OUTRAS_ENTIDADES
WHERE CPF IN
(''00000000000'',''11111111111'',''22222222222'',''33333333333'',''44444444444'',''55555555555'',
 ''66666666666'',''77777777777'',''88888888888'',''99999999999'',''99999999991'');', 'SELECT', '2026-03-25 15:24:24.595764-03');
INSERT INTO public.tb_blocos_rotina OVERRIDING SYSTEM VALUE VALUES (589, 2, '7', 7, 'DELETE FROM SCH_SGIC.AUX_USUARIO_OUTRAS_ENTIDADES
WHERE CPF IN
(''00000000000'',''11111111111'',''22222222222'',''33333333333'',''44444444444'',''55555555555'',
 ''66666666666'',''77777777777'',''88888888888'',''99999999999'',''99999999991'');', 'SELECT', '2026-03-25 15:24:24.596614-03');
INSERT INTO public.tb_blocos_rotina OVERRIDING SYSTEM VALUE VALUES (590, 2, '8', 8, 'SELECT CPF,COUNT(*)
  FROM SCH_SGIC.AUX_USUARIO_OUTRAS_ENTIDADES
 GROUP BY CPF
HAVING COUNT(*) > 1;', 'SELECT', '2026-03-25 15:24:24.597448-03');
INSERT INTO public.tb_blocos_rotina OVERRIDING SYSTEM VALUE VALUES (591, 2, '9', 9, 'SELECT * FROM SCH_SGIC.AUX_USUARIO_OUTRAS_ENTIDADES
 WHERE ID IN (SELECT MIN(ID)
          FROM SCH_SGIC.AUX_USUARIO_OUTRAS_ENTIDADES
      GROUP BY CPF
     HAVING COUNT(*) > 1);', 'SELECT', '2026-03-25 15:24:24.598127-03');
INSERT INTO public.tb_blocos_rotina OVERRIDING SYSTEM VALUE VALUES (592, 2, '10', 10, 'DELETE FROM SCH_SGIC.AUX_USUARIO_OUTRAS_ENTIDADES
 WHERE ID IN (SELECT MIN(ID)
          FROM SCH_SGIC.AUX_USUARIO_OUTRAS_ENTIDADES
      GROUP BY CPF
     HAVING COUNT(*) > 1);', 'DELETE', '2026-03-25 15:24:24.598574-03');
INSERT INTO public.tb_blocos_rotina OVERRIDING SYSTEM VALUE VALUES (593, 2, '11', 11, '-- VERIFICA INEXISTENCIA DE 11 CARACTERES PARA O CPF
-- MIN = 11
SELECT 
	MIN(CHARACTER_LENGTH(CPF))
 FROM
 	SCH_SGIC.AUX_USUARIO_OUTRAS_ENTIDADES;', 'SELECT', '2026-03-25 15:24:24.599163-03');
INSERT INTO public.tb_blocos_rotina OVERRIDING SYSTEM VALUE VALUES (594, 2, '12', 12, 'UPDATE 
	SCH_SGIC.AUX_USUARIO_OUTRAS_ENTIDADES
SET
	CPF = LPAD(TRIM(CPF),11,''0'')
WHERE
	CHARACTER_LENGTH (TRIM(CPF)) < 11;', 'UPDATE', '2026-03-25 15:24:24.599641-03');
INSERT INTO public.tb_blocos_rotina OVERRIDING SYSTEM VALUE VALUES (595, 2, '13', 13, '-- REMOVE CARACTERES NÃO NUMÉRICOS NA MATRICULA
UPDATE 
	SCH_SGIC.AUX_USUARIO_OUTRAS_ENTIDADES
SET
	MATRICULA =  REGEXP_REPLACE(MATRICULA, ''[^0-9]'', '''', ''gi'');', 'UPDATE', '2026-03-25 15:24:24.600319-03');
INSERT INTO public.tb_blocos_rotina OVERRIDING SYSTEM VALUE VALUES (596, 2, '14', 14, '-- VERIFICA INEXISTENCIA DE 11 CARACTERES PARA A MATRÍCULA
SELECT 
	MIN(CHARACTER_LENGTH(MATRICULA))
FROM
	SCH_SGIC.AUX_USUARIO_OUTRAS_ENTIDADES;', 'SELECT', '2026-03-25 15:24:24.601185-03');
INSERT INTO public.tb_blocos_rotina OVERRIDING SYSTEM VALUE VALUES (597, 2, '15', 15, 'UPDATE SCH_SGIC.AUX_USUARIO_OUTRAS_ENTIDADES
   SET MATRICULA = LPAD(TRIM(MATRICULA),11,''0'')
 WHERE CHARACTER_LENGTH (TRIM(MATRICULA)) < 11;', 'UPDATE', '2026-03-25 15:24:24.601765-03');
INSERT INTO public.tb_blocos_rotina OVERRIDING SYSTEM VALUE VALUES (598, 2, '16', 16, '-- REMOVE CARACTERES NÃO NUMÉRICOS NA MATRICULA_CHEFIA
UPDATE SCH_SGIC.AUX_USUARIO_OUTRAS_ENTIDADES
 SET MATRICULA_CHEFIA =  REGEXP_REPLACE(MATRICULA_CHEFIA, ''[^0-9]'', '''', ''gi'');', 'UPDATE', '2026-03-25 15:24:24.602601-03');
INSERT INTO public.tb_blocos_rotina OVERRIDING SYSTEM VALUE VALUES (599, 2, '17', 17, '-- VERIFICA INEXISTENCIA DE 11 CARACTERES PARA A MATRÍCULA CHEFIA
SELECT MIN(CHARACTER_LENGTH(MATRICULA_CHEFIA))
  FROM SCH_SGIC.AUX_USUARIO_OUTRAS_ENTIDADES;', 'SELECT', '2026-03-25 15:24:24.603138-03');
INSERT INTO public.tb_blocos_rotina OVERRIDING SYSTEM VALUE VALUES (600, 2, '18', 18, 'UPDATE SCH_SGIC.AUX_USUARIO_OUTRAS_ENTIDADES
   SET MATRICULA_CHEFIA = LPAD(TRIM(MATRICULA_CHEFIA),11,''0'')
 WHERE CHARACTER_LENGTH (TRIM(MATRICULA_CHEFIA)) < 11;', 'UPDATE', '2026-03-25 15:24:24.603683-03');
INSERT INTO public.tb_blocos_rotina OVERRIDING SYSTEM VALUE VALUES (601, 2, '19', 19, 'INSERT INTO SCH_SGIC.SGIC_USUARIO
SELECT nextval(''sch_sgic.sgic_usuario_id_usuario_seq''::regclass),
       NULL data_alteracao,
       NULL data_exclusao,
       CURRENT_DATE data_inclusao,
       FALSE bloqueado,
       NULL cargo_efetivo_instrutor,
       NULL categoria_orgao_origem,
       X.CPF CPF,
       NULL curriculo_instrutor,
       ''2060-01-01 00:00:00'' data_validade,
       X.EMAIL email,
       TRUE ind_ativo,
       X.NOME nome,
       NULL placa_carro_instrutor,
       TRUE recebe_notif_email,
       NULL rg_instrutor,
       ''1000:5b4240353134306661:8a075af6298092071869f6cc1cb76f6e4e312805436874d08d80b8065c4784e63d0f4e77e66099f8cba1f4638a97990b6a90ff9bd14cd43b926e71eaa9aa86f9'' senha,
       X.TELEFONE telefone,
       NULL tele_residencial_instrutor,
       NULL tipo_usuario_instrutor,
       NULL id_chefe_imediato
  FROM SCH_SGIC.AUX_USUARIO_OUTRAS_ENTIDADES X
 WHERE NOT EXISTS (SELECT CPF
                     FROM SCH_SGIC.SGIC_USUARIO U
                   WHERE U.CPF = X.CPF) order by 8;', 'INSERT', '2026-03-25 15:24:24.604205-03');
INSERT INTO public.tb_blocos_rotina OVERRIDING SYSTEM VALUE VALUES (602, 2, '20', 20, '--No Update lembrar de excluir logicamente usuario_outras_entidades caso tenha alterado a entidade do vínculo atual
--Incluir o registro em usuario_outras_entidades do novo vínculo

--A-CPF não veio no arquivo de outras entidades
--B-Excluir o id_usuario na tabela usuario_outras_entidades
--C-Excluir logicamente apenas os que não estejam nas empresas e categorias citadas no WHERE.
SELECT *
  FROM sch_sgic.sgic_usuario u JOIN sch_sgic.sgic_usuario_outras_entidades uo
    ON U.id_usuario = uo.id_usuario
 WHERE UO.IND_ATIVO = TRUE
   AND U.IND_ATIVO = TRUE
   --VERIFICA SE O USUÁRIO ENCONTRA-SE NO ARQUIVO EXCEL(.CSV)
   AND NOT EXISTS (SELECT 1
                     FROM sch_sgic.aux_usuario_outras_entidades x
                    WHERE x.cpf = u.cpf
                      and lpad(x.matricula,11,''0'') = lpad(uo.matricula,11,''0''))
   --VERIFICA SE O USUARIO FAZ PARTE DO SADRH
   AND NOT EXISTS (SELECT *
                     FROM sch_sad.sad_servidor s JOIN sch_sad.sad_vinculo v
                       ON s.codigo = v.codigo_sad_servidor
                    WHERE s.cpf = u.cpf
                      AND s.ind_ativo = ''1''
                      AND v.ind_ativo = ''1''
                      AND v.codigo_srh_empresa in (''029'',''045'',''047'',''133'')
                      AND v.codigo_srh_categoria_funcional in (''APO'',''RF'',''RFB'',''RR'',''RRB''))
   --VERIFICA SE A EMPRESA ENCONTRA-SE NO ARQUIVO EXCEL(.CSV)
   AND EXISTS (SELECT distinct id_outras_entidades
                 FROM sch_sgic.aux_usuario_outras_entidades x2
                WHERE x2.id_outras_entidades = uo.id_outras_entidades);
', 'SELECT', '2026-03-25 15:24:24.604709-03');
INSERT INTO public.tb_blocos_rotina OVERRIDING SYSTEM VALUE VALUES (603, 2, '21', 21, 'UPDATE SCH_SGIC.SGIC_USUARIO_OUTRAS_ENTIDADES
   SET IND_ATIVO = FALSE
 WHERE IND_ATIVO = TRUE
   AND ID_USUARIO IN (SELECT U.ID_USUARIO
            FROM sch_sgic.sgic_usuario u JOIN sch_sgic.sgic_usuario_outras_entidades uo
                          ON U.id_usuario = uo.id_usuario
                       WHERE U.IND_ATIVO = TRUE
                         AND NOT EXISTS (SELECT 1
                                           FROM sch_sgic.aux_usuario_outras_entidades x
                                          WHERE x.cpf = u.cpf
                                            AND lpad(x.matricula,11,''0'') = lpad(uo.matricula,11,''0''))
       AND NOT EXISTS (SELECT *
                                           FROM sch_sad.sad_servidor s JOIN sch_sad.sad_vinculo v
                                             ON s.codigo = v.codigo_sad_servidor
                                          WHERE s.cpf = u.cpf
                                            AND s.ind_ativo = ''1''
                                            AND v.ind_ativo = ''1''
                                            AND v.codigo_srh_empresa in (''029'',''045'',''047'',''133'')
                                            AND v.codigo_srh_categoria_funcional in (''APO'',''RF'',''RFB'',''RR'',''RRB''))
                         AND EXISTS (SELECT distinct id_outras_entidades
                                       FROM sch_sgic.aux_usuario_outras_entidades x2
                                      WHERE x2.id_outras_entidades = uo.id_outras_entidades));', 'UPDATE', '2026-03-25 15:24:24.6052-03');
INSERT INTO public.tb_blocos_rotina OVERRIDING SYSTEM VALUE VALUES (604, 2, '22', 22, 'INSERT INTO sch_sgic.sgic_usuario_outras_entidades(
            id_usuario, ind_ativo, matricula, id_outras_entidades)
SELECT u.id_usuario,
       true,
       LPAD(TRIM(a.matricula),11,''0'') matricula,
       a.id_outras_entidades
  from sch_sgic.sgic_usuario u join sch_sgic.aux_usuario_outras_entidades a
    on u.cpf = a.cpf
 where not exists (select 1
                       from sch_sgic.sgic_usuario_outras_entidades uo
                      where uo.id_usuario = u.id_usuario
                        and LPAD(a.matricula,11,''0'') = LPAD(uo.matricula,11,''0'')
                        and a.id_outras_entidades = uo.id_outras_entidades)
     AND NOT EXISTS (SELECT 1
                     FROM sch_sad.sad_servidor s JOIN sch_sad.sad_vinculo v
                       ON s.codigo = v.codigo_sad_servidor
                    WHERE s.cpf = u.cpf
                      AND s.ind_ativo = ''1''
                      AND v.ind_ativo = ''1''
                      AND v.codigo_srh_empresa in (''029'',''045'',''047'',''133'')
                      AND v.codigo_srh_categoria_funcional in (''APO'',''RF'',''RFB'',''RR'',''RRB''));', 'INSERT', '2026-03-25 15:24:24.605652-03');
INSERT INTO public.tb_blocos_rotina OVERRIDING SYSTEM VALUE VALUES (605, 2, '23', 23, '-------------------------------------------------------------
--REATIVANDO USUÁRIOS EXISTENTES NA BASE QUE ESTÃO NO ARQUIVO
-------------------------------------------------------------
SELECT CPF,
       NOME
  FROM SCH_SGIC.SGIC_USUARIO U
 WHERE U.IND_ATIVO = FALSE
   AND EXISTS (SELECT X.CPF
                 FROM SCH_SGIC.AUX_USUARIO_OUTRAS_ENTIDADES X
                WHERE U.CPF = X.CPF);', 'SELECT', '2026-03-25 15:24:24.606081-03');
INSERT INTO public.tb_blocos_rotina OVERRIDING SYSTEM VALUE VALUES (606, 2, '24', 24, 'UPDATE SCH_SGIC.SGIC_USUARIO U
   SET IND_ATIVO = TRUE
WHERE U.IND_ATIVO = FALSE
  AND EXISTS (SELECT X.CPF
               FROM SCH_SGIC.AUX_USUARIO_OUTRAS_ENTIDADES X
              WHERE U.CPF = X.CPF);', 'UPDATE', '2026-03-25 15:24:24.606517-03');
INSERT INTO public.tb_blocos_rotina OVERRIDING SYSTEM VALUE VALUES (607, 2, '25', 25, '------------------------------
--ATUALIZANDO O CHEFE IMEDIATO
------------------------------
SELECT *
  FROM SCH_SGIC.SGIC_USUARIO U JOIN (SELECT DISTINCT C.CPF_FUNC,
U2.ID_USUARIO
FROM SCH_SGIC.SGIC_USUARIO U2 JOIN (SELECT X.CPF AS CPF_FUNC,
                                X2.CPF AS CPF_CHEFE
                            FROM SCH_SGIC.AUX_USUARIO_OUTRAS_ENTIDADES X
            JOIN SCH_SGIC.AUX_USUARIO_OUTRAS_ENTIDADES X2
            ON X.MATRICULA_CHEFIA = X2.MATRICULA) C
ON U2.CPF = C.CPF_CHEFE) UC
ON U.CPF = UC.CPF_FUNC;', 'SELECT', '2026-03-25 15:24:24.606969-03');
INSERT INTO public.tb_blocos_rotina OVERRIDING SYSTEM VALUE VALUES (612, 2, '30', 30, '-------------------------------------------------
-- Exclusão de registros com a matrícula repetida
-------------------------------------------------
DELETE FROM SCH_SGIC.SGIC_USUARIO_OUTRAS_ENTIDADES T1
USING SCH_SGIC.SGIC_USUARIO_OUTRAS_ENTIDADES T2
WHERE LENGTH(T2.MATRICULA) < 11
AND T1.ID_USUARIO = T2.ID_USUARIO
AND T1.ID_OUTRAS_ENTIDADES = T2.ID_OUTRAS_ENTIDADES
AND T1.MATRICULA = LPAD(TRIM(T2.MATRICULA),11,''0'');', 'DELETE', '2026-03-25 15:24:24.609479-03');
INSERT INTO public.tb_blocos_rotina OVERRIDING SYSTEM VALUE VALUES (613, 2, '31', 31, '-- Tratar o valor da coluna matrícula com 11 caracteres
UPDATE SCH_SGIC.SGIC_USUARIO_OUTRAS_ENTIDADES
 SET MATRICULA = LPAD(TRIM(MATRICULA),11,''0'')
WHERE LENGTH(MATRICULA) < 11;', 'UPDATE', '2026-03-25 15:24:24.609897-03');
INSERT INTO public.tb_blocos_rotina OVERRIDING SYSTEM VALUE VALUES (614, 2, '32', 32, '--------------------------------------------------------------------------------------------
-- Consulta a quantidade de usuários outras entidades na tabela sch_sgic.sgic_usuario_escola
--------------------------------------------------------------------------------------------
select count(ue.id_usuario)
from sch_sgic.sgic_usuario_escola ue
join sch_sgic.sgic_usuario_outras_entidades uoe on ue.id_usuario = uoe.id_usuario
where ue.id_escola in (
  select e.id_escola
  from sch_sgic.sgic_escola e
  where e.ind_atende_todas_instituicoes = true
);', 'SELECT', '2026-03-25 15:24:24.610328-03');
INSERT INTO public.tb_blocos_rotina OVERRIDING SYSTEM VALUE VALUES (615, 2, '33', 33, '--------------------------------------------------------------------------------
-- Exclusão dos usuários outras entidades na tabela sch_sgic.sgic_usuario_escola
--------------------------------------------------------------------------------
delete from sch_sgic.sgic_usuario_escola ue
using sch_sgic.sgic_usuario_outras_entidades uoe
where ue.id_usuario = uoe.id_usuario
and ue.id_escola in (
  select e.id_escola
  from sch_sgic.sgic_escola e
  where e.ind_atende_todas_instituicoes = true
);', 'DELETE', '2026-03-25 15:24:24.610787-03');
INSERT INTO public.tb_blocos_rotina OVERRIDING SYSTEM VALUE VALUES (616, 2, '34', 34, '--------------------------------------------------------------------------------
-- Inclusão dos usuários outras entidades na tabela sch_sgic.sgic_usuario_escola
--------------------------------------------------------------------------------
insert into sch_sgic.sgic_usuario_escola (id_usuario, ind_ativo, matricula, codigo_instituicao, id_escola, id_outras_entidades)
select u.id_usuario
      , CASE WHEN uoe.ind_ativo = ''1'' THEN true else false end ind_ativo
      , LPAD( uoe.matricula, 11, ''0'')
      , 0 codigo_instituicao
      , e.id_escola
      , uoe.id_outras_entidades
from sch_sgic.sgic_usuario_outras_entidades uoe
join sch_sgic.sgic_usuario u on uoe.id_usuario = u.id_usuario
join sch_sgic.sgic_escola e on e.ind_atende_todas_instituicoes = true
where uoe.ind_ativo = ''1'';', 'INSERT', '2026-03-25 15:24:24.611206-03');
INSERT INTO public.tb_blocos_rotina OVERRIDING SYSTEM VALUE VALUES (617, 2, '35', 35, 'DROP TABLE IF EXISTS SCH_SGIC.AUX_USUARIO_OUTRAS_ENTIDADES;', 'DROP', '2026-03-25 15:24:24.611601-03');
INSERT INTO public.tb_blocos_rotina OVERRIDING SYSTEM VALUE VALUES (618, 2, '36', 36, 'DROP TABLE IF EXISTS sch_sgic.bkp_sgic_outras_entidades;', 'DROP', '2026-03-25 15:24:24.612-03');


--
-- TOC entry 5560 (class 0 OID 46570)
-- Dependencies: 290
-- Data for Name: tb_canais_notificacao; Type: TABLE DATA; Schema: public; Owner: postgres
--



--
-- TOC entry 5551 (class 0 OID 46469)
-- Dependencies: 281
-- Data for Name: tb_compartilhamentos; Type: TABLE DATA; Schema: public; Owner: postgres
--



--
-- TOC entry 5554 (class 0 OID 46524)
-- Dependencies: 284
-- Data for Name: tb_configuracoes; Type: TABLE DATA; Schema: public; Owner: postgres
--

INSERT INTO public.tb_configuracoes VALUES ('login_bg_imagem', 'https://img.freepik.com/free-vector/creative-abstract-sql-illustration_52683-79681.jpg', 'geral', NULL, '2026-03-24 10:30:53.49058-03', 1);
INSERT INTO public.tb_configuracoes VALUES ('modo_manutencao', '0', 'geral', NULL, '2026-03-24 10:30:53.491861-03', 1);
INSERT INTO public.tb_configuracoes VALUES ('sessao_tempo', '120', 'seguranca', NULL, '2026-03-20 16:41:23.921966-03', 1);
INSERT INTO public.tb_configuracoes VALUES ('login_tentativas', '5', 'seguranca', NULL, '2026-03-20 16:41:23.924046-03', 1);
INSERT INTO public.tb_configuracoes VALUES ('login_bloqueio', '15', 'seguranca', NULL, '2026-03-20 16:41:23.925137-03', 1);
INSERT INTO public.tb_configuracoes VALUES ('senha_min', '8', 'seguranca', NULL, '2026-03-20 16:41:23.926153-03', 1);
INSERT INTO public.tb_configuracoes VALUES ('senha_maiuscula', '1', 'seguranca', NULL, '2026-03-20 16:41:23.927142-03', 1);
INSERT INTO public.tb_configuracoes VALUES ('senha_minuscula', '1', 'seguranca', NULL, '2026-03-20 16:41:23.928095-03', 1);
INSERT INTO public.tb_configuracoes VALUES ('senha_numero', '1', 'seguranca', NULL, '2026-03-20 16:41:23.929325-03', 1);
INSERT INTO public.tb_configuracoes VALUES ('senha_especial', '0', 'seguranca', NULL, '2026-03-20 16:41:23.931134-03', 1);
INSERT INTO public.tb_configuracoes VALUES ('2fa_ativo', '0', 'seguranca', NULL, '2026-03-20 16:41:23.932784-03', 1);
INSERT INTO public.tb_configuracoes VALUES ('app_manutencao', '0', 'geral', 'Modo manutenção', '2026-03-20 16:35:23.965454-03', 1);
INSERT INTO public.tb_configuracoes VALUES ('ldap_filtro', '(sAMAccountName={username})', 'ldap', NULL, '2026-03-20 16:35:24.091994-03', 1);
INSERT INTO public.tb_configuracoes VALUES ('smtp_host', 'smtp.hostinger.com', 'email', 'Servidor SMTP', '2026-03-24 13:44:00.10398-03', 1);
INSERT INTO public.tb_configuracoes VALUES ('smtp_port', '587', 'email', 'Porta SMTP', '2026-03-24 13:44:00.105041-03', 1);
INSERT INTO public.tb_configuracoes VALUES ('smtp_encryption', 'tls', 'email', 'Criptografia SMTP', '2026-03-24 13:44:00.108122-03', 1);
INSERT INTO public.tb_configuracoes VALUES ('seguranca_timeout_sessao', '120', 'seguranca', 'Timeout de sessão em segundos', '2026-03-20 16:35:24.623038-03', 1);
INSERT INTO public.tb_configuracoes VALUES ('seguranca_tentativas_login', '5', 'seguranca', 'Tentativas de login antes de bloqueio', '2026-03-20 16:35:24.625081-03', 1);
INSERT INTO public.tb_configuracoes VALUES ('seguranca_tempo_bloqueio', '15', 'seguranca', 'Tempo de bloqueio em segundos', '2026-03-20 16:35:24.626939-03', 1);
INSERT INTO public.tb_configuracoes VALUES ('notif_email_falha', '1', 'notificacoes', 'Enviar e-mail em falha de execução', '2026-03-20 16:35:24.875573-03', 1);
INSERT INTO public.tb_configuracoes VALUES ('notif_webhook_ativo', '0', 'notificacoes', 'Ativar webhooks de notificação', '2026-03-20 16:35:24.876996-03', 1);
INSERT INTO public.tb_configuracoes VALUES ('notif_webhook_url', '', 'notificacoes', 'URL do webhook padrão', '2026-03-20 16:35:24.878393-03', 1);
INSERT INTO public.tb_configuracoes VALUES ('notif_email_sucesso', '0', 'notificacoes', NULL, '2026-03-20 16:35:24.880192-03', 1);
INSERT INTO public.tb_configuracoes VALUES ('smtp_user', 'naoresponder@dynamicmotioncentury.com.br', 'email', 'Usuário SMTP', '2026-03-24 13:44:00.110892-03', 1);
INSERT INTO public.tb_configuracoes VALUES ('smtp_password', '[@uyhXK8[Mx', 'email', 'Senha SMTP', '2026-03-24 13:44:00.114128-03', 1);
INSERT INTO public.tb_configuracoes VALUES ('smtp_from_email', 'naoresponder@dynamicmotioncentury.com.br', 'email', 'E-mail remetente', '2026-03-24 13:44:00.116243-03', 1);
INSERT INTO public.tb_configuracoes VALUES ('smtp_from_name', 'DMC DataLoad', 'email', 'Nome remetente', '2026-03-24 13:44:00.117495-03', 1);
INSERT INTO public.tb_configuracoes VALUES ('notif_falha', '1', 'notificacoes', NULL, '2026-03-24 15:23:29.646549-03', 1);
INSERT INTO public.tb_configuracoes VALUES ('notif_sucesso', '1', 'notificacoes', NULL, '2026-03-24 15:23:29.647501-03', 1);
INSERT INTO public.tb_configuracoes VALUES ('notif_conexao', '1', 'notificacoes', NULL, '2026-03-24 15:23:29.648507-03', 1);
INSERT INTO public.tb_configuracoes VALUES ('ldap_host', 'ldap.test.local', 'ldap', NULL, '2026-03-24 09:16:06.520698-03', 1);
INSERT INTO public.tb_configuracoes VALUES ('ldap_port', '389', 'ldap', NULL, '2026-03-24 09:16:06.521606-03', 1);
INSERT INTO public.tb_configuracoes VALUES ('ldap_ssl', '0', 'ldap', NULL, '2026-03-24 09:16:06.522597-03', 1);
INSERT INTO public.tb_configuracoes VALUES ('ldap_base_dn', 'dc=test,dc=local', 'ldap', NULL, '2026-03-24 09:16:06.523621-03', 1);
INSERT INTO public.tb_configuracoes VALUES ('ip_tentativas', '10', 'seguranca', NULL, '2026-03-20 17:25:48.329404-03', NULL);
INSERT INTO public.tb_configuracoes VALUES ('ip_bloqueio', '15', 'seguranca', NULL, '2026-03-20 17:25:48.330697-03', NULL);
INSERT INTO public.tb_configuracoes VALUES ('notif_emails', '', 'notificacoes', NULL, '2026-03-24 15:23:29.649599-03', 1);
INSERT INTO public.tb_configuracoes VALUES ('notif_agendamento', '0', 'notificacoes', NULL, '2026-03-24 15:23:29.651121-03', 1);
INSERT INTO public.tb_configuracoes VALUES ('ldap_filter', '(sAMAccountName={username})', 'ldap', NULL, '2026-03-24 09:16:06.524179-03', 1);
INSERT INTO public.tb_configuracoes VALUES ('ldap_bind_dn', 'cn=admin,dc=test,dc=local', 'ldap', NULL, '2026-03-24 09:16:06.52474-03', 1);
INSERT INTO public.tb_configuracoes VALUES ('ldap_bind_password', 'testpass123', 'ldap', NULL, '2026-03-24 09:16:06.525295-03', 1);
INSERT INTO public.tb_configuracoes VALUES ('ldap_ativo', '0', 'ldap', NULL, '2026-03-24 09:16:06.525857-03', 1);
INSERT INTO public.tb_configuracoes VALUES ('notif_sistema', '0', 'notificacoes', NULL, '2026-03-24 15:23:29.653158-03', 1);
INSERT INTO public.tb_configuracoes VALUES ('app_favicon', '', 'geral', NULL, '2026-03-24 10:27:57.960451-03', 1);
INSERT INTO public.tb_configuracoes VALUES ('scheduler_ativo', '1', 'scheduler', NULL, '2026-03-25 15:58:12.06547-03', 1);
INSERT INTO public.tb_configuracoes VALUES ('scheduler_intervalo', '30', 'scheduler', 'Intervalo de verificação em segundos', '2026-03-25 15:58:12.067003-03', 1);
INSERT INTO public.tb_configuracoes VALUES ('scheduler_max_paralelo', '10', 'scheduler', 'Máximo de execuções paralelas', '2026-03-25 15:58:12.068245-03', 1);
INSERT INTO public.tb_configuracoes VALUES ('app_nome', 'DMC - DataLoad', 'geral', 'Nome da aplicação', '2026-03-24 10:30:53.481291-03', 1);
INSERT INTO public.tb_configuracoes VALUES ('app_url', 'http://localhost/DMC-DATALOAD/public', 'geral', NULL, '2026-03-24 10:30:53.48359-03', 1);
INSERT INTO public.tb_configuracoes VALUES ('app_timezone', 'America/Sao_Paulo', 'geral', 'Timezone padrão', '2026-03-24 10:30:53.485716-03', 1);
INSERT INTO public.tb_configuracoes VALUES ('app_idioma', 'pt_BR', 'geral', 'Idioma padrão', '2026-03-24 10:30:53.487318-03', 1);
INSERT INTO public.tb_configuracoes VALUES ('app_descricao', 'Sistema para bancos de dados', 'geral', NULL, '2026-03-24 10:30:53.488923-03', 1);
INSERT INTO public.tb_configuracoes VALUES ('scheduler_timeout', '3600', 'scheduler', 'Timeout de execução em segundos', '2026-03-25 15:58:12.069251-03', 1);
INSERT INTO public.tb_configuracoes VALUES ('scheduler_retry', '1', 'scheduler', NULL, '2026-03-25 15:58:12.070251-03', 1);
INSERT INTO public.tb_configuracoes VALUES ('scheduler_max_tentativas', '3', 'scheduler', NULL, '2026-03-25 15:58:12.071237-03', 1);
INSERT INTO public.tb_configuracoes VALUES ('scheduler_intervalo_retry', '600', 'scheduler', NULL, '2026-03-25 15:58:12.072233-03', 1);


--
-- TOC entry 5539 (class 0 OID 46345)
-- Dependencies: 269
-- Data for Name: tb_empresas; Type: TABLE DATA; Schema: public; Owner: postgres
--

INSERT INTO public.tb_empresas VALUES (1, 'Secretariá de administração', '', true, 1, '2026-03-19 19:25:43.513597-03', '2026-03-19 19:25:43.513597-03');


--
-- TOC entry 5517 (class 0 OID 45732)
-- Dependencies: 247
-- Data for Name: tb_eventos_api; Type: TABLE DATA; Schema: public; Owner: postgres
--



--
-- TOC entry 5558 (class 0 OID 46550)
-- Dependencies: 288
-- Data for Name: tb_fila_execucao; Type: TABLE DATA; Schema: public; Owner: postgres
--



--
-- TOC entry 5505 (class 0 OID 45607)
-- Dependencies: 234
-- Data for Name: tb_logs_execucao; Type: TABLE DATA; Schema: public; Owner: postgres
--

INSERT INTO public.tb_logs_execucao OVERRIDING SYSTEM VALUE VALUES (22, 4, '2026-03-26 13:17:03.750269-03', '2026-03-26 13:17:03.750269-03', 'sucesso', NULL, '{"blocos": [{"sql": "-- CRIACAO DE TABELA DE LOG DE CARGA SGIC. CRIAR APENAS SE NÃO EXISTIR.\nCREATE TABLE IF NOT EXISTS sch_sgic.log_carga_sgic\n(\n  id serial4 primary key,\n  numero_script CHAR(3),\n  momento_execucao TIMESTAMP,\n  descricao CHARACTER VARYING (100)\n)\nWITH (\n  OIDS=FALSE\n);", "erro": null, "tipo": "CREATE", "bloco": "1", "ordem": 1, "status": "sucesso", "id_bloco": 558, "registros": 0, "resultado": "Linhas afetadas: 0", "duracao_ms": 142, "arquivo_csv": null}, {"sql": "ALTER TABLE sch_sgic.log_carga_sgic OWNER TO saduseradm;", "erro": null, "tipo": "ALTER", "bloco": "2", "ordem": 2, "status": "sucesso", "id_bloco": 559, "registros": 0, "resultado": "Linhas afetadas: 0", "duracao_ms": 21, "arquivo_csv": null}, {"sql": "GRANT ALL ON TABLE sch_sgic.log_carga_sgic TO saduseradm;", "erro": null, "tipo": "GRANT", "bloco": "3", "ordem": 3, "status": "sucesso", "id_bloco": 560, "registros": 0, "resultado": "Linhas afetadas: 0", "duracao_ms": 26, "arquivo_csv": null}, {"sql": "GRANT ALL ON TABLE sch_sgic.log_carga_sgic TO sadsisweb;", "erro": null, "tipo": "SELECT", "bloco": "4", "ordem": 4, "status": "sucesso", "id_bloco": 561, "registros": 0, "resultado": "Linhas afetadas: 0", "duracao_ms": 21, "arquivo_csv": null}, {"sql": "GRANT SELECT ON TABLE sch_sgic.log_carga_sgic TO saduserr;", "erro": null, "tipo": "GRANT", "bloco": "5", "ordem": 5, "status": "sucesso", "id_bloco": 562, "registros": 0, "resultado": "Linhas afetadas: 0", "duracao_ms": 22, "arquivo_csv": null}, {"sql": "GRANT ALL ON ALL TABLES IN SCHEMA sch_sad TO saduseradm;", "erro": null, "tipo": "GRANT", "bloco": "6", "ordem": 6, "status": "sucesso", "id_bloco": 563, "registros": 0, "resultado": "Linhas afetadas: 0", "duracao_ms": 468, "arquivo_csv": null}, {"sql": "GRANT SELECT, INSERT, UPDATE, DELETE ON ALL TABLES IN SCHEMA sch_sad TO sadsisweb;", "erro": null, "tipo": "GRANT", "bloco": "7", "ordem": 7, "status": "sucesso", "id_bloco": 564, "registros": 0, "resultado": "Linhas afetadas: 0", "duracao_ms": 188, "arquivo_csv": null}, {"sql": "GRANT ALL ON ALL SEQUENCES IN SCHEMA sch_sad TO sadsisweb;", "erro": null, "tipo": "GRANT", "bloco": "8", "ordem": 8, "status": "sucesso", "id_bloco": 565, "registros": 0, "resultado": "Linhas afetadas: 0", "duracao_ms": 211, "arquivo_csv": null}, {"sql": "SELECT \n\tcount(0)\nFROM \n\tsch_sgic.sgic_usuario u\nWHERE \n\tu.ind_ativo = TRUE\n\tAND EXISTS (\n    SELECT 1\n    FROM sch_sad.imp_servidor s\n    WHERE s.cpf = u.cpf\n     AND s.ind_ativo = ''0''\n  )\n\tAND NOT EXISTS (\n    SELECT 1\n    FROM sch_sgic.sgic_usuario_outras_entidades uo\n    WHERE u.id_usuario = uo.id_usuario\n );", "erro": null, "tipo": "SELECT", "bloco": "9", "ordem": 9, "status": "sucesso", "id_bloco": 566, "registros": 1, "resultado": "Arquivo gerado: C:\\xampp\\htdocs\\DMC-DATALOAD\\storage\\logs\\execucao_4_9_1774541746.csv (Linhas: 1)", "duracao_ms": 1477, "arquivo_csv": "C:\\xampp\\htdocs\\DMC-DATALOAD\\storage\\logs\\execucao_4_9_1774541746.csv"}, {"sql": "UPDATE \n\tsch_sgic.sgic_usuario u\nSET \n\tind_ativo = FALSE\n\t,data_alteracao = current_date\nWHERE \n\tu.ind_ativo = TRUE\n\tAND EXISTS (\n    SELECT 1\n    FROM sch_sad.imp_servidor s\n    WHERE s.cpf = u.cpf\n    AND s.ind_ativo = ''0''\n )\n\tAND NOT EXISTS (\n    \tSELECT 1\n      FROM sch_sgic.sgic_usuario_outras_entidades uo\n      WHERE u.id_usuario = uo.id_usuario\n );", "erro": null, "tipo": "UPDATE", "bloco": "10", "ordem": 10, "status": "sucesso", "id_bloco": 567, "registros": 0, "resultado": "Linhas afetadas: 0", "duracao_ms": 929, "arquivo_csv": null}, {"sql": "INSERT INTO sch_sgic.sgic_usuario(\n            data_inclusao,\n            bloqueado,\n            cargo_efetivo_instrutor,\n            categoria_orgao_origem,\n            cpf,\n            curriculo_instrutor,\n            data_validade,\n            email,\n            ind_ativo,\n            nome,\n            placa_carro_instrutor,\n            recebe_notif_email,\n            rg_instrutor,\n            senha,\n            telefone,\n            tele_residencial_instrutor,\n            tipo_usuario_instrutor)\nSELECT DISTINCT current_date, -- data_inclusao\n       FALSE, --bloqueado\n       NULL, --cargo_efetivo\n       NULL, --categoria_orgao_origem\n       cpf,\n       NULL, --curriculo\n       CAST(''2060-01-01 00:00:00'' as timestamp), --data_validade,\n       NULL,--v.email, --email\n       CASE WHEN s.ind_ativo = ''0'' THEN FALSE ELSE TRUE END, --ind_ativo,\n       nome, --nome\n       NULL, --placa_carro\n       CAST(TRUE as boolean), --recebe_notif_email\n       NULL, --rg_instrutor\n       ''CAMPO SENHA PARA USUARIOS DE OUTRAS ENTIDADES'', --senha\n       NULL,--ddd_telefone || telefone, --telefone\n       NULL, --tele_residencial\n       NULL --tipo_usuario\nFROM sch_sad.imp_servidor s JOIN sch_sad.imp_vinculo v\nON s.codigo = v.codigo_sad_servidor\nWHERE not exists (SELECT cpf\n                    FROM sch_sgic.sgic_usuario u\n                    WHERE u.cpf = s.cpf)\nAND s.ind_ativo = ''1''\nAND v.ind_ativo = ''1''\nAND v.codigo_srh_categoria_funcional IN (''AGP'',''CCB'',''CIV'',''CLT'',''COM'',''CTD'',''DES'',''ELE'',''EST'',\n''EXQ'',''EXM'',''FRQ'',''GAP'',''MCB'',''MLT'',''REV'',''VOG'');", "erro": null, "tipo": "INSERT", "bloco": "11", "ordem": 11, "status": "sucesso", "id_bloco": 568, "registros": 0, "resultado": "Linhas afetadas: 0", "duracao_ms": 1993, "arquivo_csv": null}, {"sql": "SELECT \n\tcount(*)\nFROM \n\tsch_sad.imp_servidor s \n  JOIN sch_sad.imp_vinculo v \tON s.codigo = v.codigo_sad_servidor\n\tJOIN sch_sgic.sgic_usuario u ON s.cpf = u.cpf\nWHERE\n\ts.ind_ativo = ''1''\n\tAND v.ind_ativo = ''1''\n\tAND v.codigo_srh_categoria_funcional in (''AGP'',''CCB'',''CIV'',''CLT'',''COM'',''CTD'',''DES'',''ELE'',''EST'',''GAP'',''MCB'',''MLT'',''REV'',''VOG'')\n\tAND u.nome <> s.nome\n;", "erro": null, "tipo": "SELECT", "bloco": "12", "ordem": 12, "status": "sucesso", "id_bloco": 569, "registros": 1, "resultado": "Arquivo gerado: C:\\xampp\\htdocs\\DMC-DATALOAD\\storage\\logs\\execucao_4_12_1774541751.csv (Linhas: 1)", "duracao_ms": 1324, "arquivo_csv": "C:\\xampp\\htdocs\\DMC-DATALOAD\\storage\\logs\\execucao_4_12_1774541751.csv"}, {"sql": "UPDATE \n\tsch_sgic.sgic_usuario u\nSET \n\tnome = s.nome\nFROM\n\tsch_sad.imp_servidor s JOIN sch_sad.imp_vinculo v ON s.codigo = v.codigo_sad_servidor\nWHERE\n\ts.cpf = u.cpf\n\tAND s.ind_ativo = ''1''\n\tAND v.ind_ativo = ''1''\n\tAND v.codigo_srh_categoria_funcional in (''AGP'',''CCB'',''CIV'',''CLT'',''COM'',''CTD'',''DES'',''ELE'',''EST'',''GAP'',''MCB'',''MLT'',''REV'',''VOG'')\n\tAND u.nome <> s.nome\n;", "erro": null, "tipo": "UPDATE", "bloco": "13", "ordem": 13, "status": "sucesso", "id_bloco": 570, "registros": 0, "resultado": "Linhas afetadas: 0", "duracao_ms": 1892, "arquivo_csv": null}, {"sql": "TRUNCATE TABLE SCH_SAD.SAD_INSTITUICAO CASCADE;", "erro": null, "tipo": "TRUNCATE", "bloco": "14", "ordem": 14, "status": "sucesso", "id_bloco": 571, "registros": 0, "resultado": "Linhas afetadas: 0", "duracao_ms": 164, "arquivo_csv": null}, {"sql": "TRUNCATE TABLE SCH_SAD.SAD_VINCULO CASCADE;", "erro": null, "tipo": "TRUNCATE", "bloco": "15", "ordem": 15, "status": "sucesso", "id_bloco": 572, "registros": 0, "resultado": "Linhas afetadas: 0", "duracao_ms": 237, "arquivo_csv": null}, {"sql": "TRUNCATE TABLE SCH_SAD.SAD_SERVIDOR CASCADE;", "erro": null, "tipo": "TRUNCATE", "bloco": "16", "ordem": 16, "status": "sucesso", "id_bloco": 657, "registros": 0, "resultado": "Linhas afetadas: 0", "duracao_ms": 250, "arquivo_csv": null}, {"sql": "INSERT INTO SCH_SAD.SAD_INSTITUICAO SELECT * FROM SCH_SAD.IMP_INSTITUICAO;", "erro": null, "tipo": "INSERT", "bloco": "17", "ordem": 17, "status": "sucesso", "id_bloco": 573, "registros": 185, "resultado": "Linhas afetadas: 185", "duracao_ms": 145, "arquivo_csv": null}, {"sql": "INSERT INTO SCH_SAD.SAD_SERVIDOR SELECT * FROM SCH_SAD.IMP_SERVIDOR;", "erro": null, "tipo": "INSERT", "bloco": "18", "ordem": 18, "status": "sucesso", "id_bloco": 574, "registros": 328244, "resultado": "Linhas afetadas: 328244", "duracao_ms": 6303, "arquivo_csv": null}, {"sql": "INSERT INTO SCH_SAD.SAD_VINCULO\nSELECT codigo, ind_ativo, matricula, codigo_sad_instituicao, nome_sad_instituicao,\n  data_efetivo_exercicio, ind_lider, ind_vinculo_exercicio, codigo_srh_cargo,\n  nome_sad_cargo, codigo_srh_cargo_origem, nome_cargo_origem, matricula_origem,\n  codigo_instituicao_origem, codigo_srh_funcao, nome_sad_funcao, codigo_srh_cargo_comissionado,\n  nome_cargo_comissionado, codigo_srh_funcao_gratificada, nome_funcao_gratificada, codigo_instituicao_chefia_imediata,\n  matricula_chefe_sad_servidor, codigo_srh_estrut_orgn, nome_sad_estrut_orgn, codigo_srh_categoria_funcional,\n  descricao_sad_categoria_funcional, codigo_srh_situacao_funcional, descricao_sad_situacao_funcional, codigo_srh_tipo_vinculo,\n  descricao_sad_tipo_vinculo, email, codigo_srh_afastamento, descricao_afastamento, codigo_srh_grupo_ocupacional, descricao_sad_grupo_ocupacional,\n  data_hora_incl, data_hora_altr, usuario_incl, usuario_altr, codigo_sad_servidor, tab_sal_cargo, tab_sal_funcao,\n  codigo_srh_grupo_ocupacional_origem, codigo_sad_empresa_estab, codigo_srh_empresa, codigo_srh_estabelecimento,\n  ind_atualizacao_srh, codigo_empresa_estab_origem, emp_origem, ind_busca_origem, ind_preenchimento,\n  matricula_origem_sadrh, matricula_chefe_sad_servidor_sadrh, codigo_srh_empresa_sadrh, codigo_srh_estabelecimento_sadrh,\n  codigo_srh_categoria_funcional_sadrh, descricao_sad_categoria_funcional_sadrh, codigo_srh_tipo_vinculo_sadrh,\n  descricao_sad_tipo_vinculo_sadrh, codigo_sad_servidor_sadrh, matricula_sadrh\nFROM sch_sad.imp_vinculo;", "erro": null, "tipo": "INSERT", "bloco": "19", "ordem": 19, "status": "sucesso", "id_bloco": 575, "registros": 605951, "resultado": "Linhas afetadas: 605951", "duracao_ms": 39568, "arquivo_csv": null}, {"sql": "SELECT SETVAL(''sch_sad.sad_servidor_codigo_seq'', MAX(CODIGO)) FROM sch_sad.sad_servidor;", "erro": null, "tipo": "SELECT", "bloco": "20", "ordem": 20, "status": "sucesso", "id_bloco": 576, "registros": 1, "resultado": "Arquivo gerado: C:\\xampp\\htdocs\\DMC-DATALOAD\\storage\\logs\\execucao_4_20_1774541800.csv (Linhas: 1)", "duracao_ms": 74, "arquivo_csv": "C:\\xampp\\htdocs\\DMC-DATALOAD\\storage\\logs\\execucao_4_20_1774541800.csv"}, {"sql": "SELECT SETVAL(''sch_sad.sad_vinculo_codigo_seq'' , MAX(CODIGO)) FROM sch_sad.sad_vinculo;", "erro": null, "tipo": "SELECT", "bloco": "21", "ordem": 21, "status": "sucesso", "id_bloco": 577, "registros": 1, "resultado": "Arquivo gerado: C:\\xampp\\htdocs\\DMC-DATALOAD\\storage\\logs\\execucao_4_21_1774541800.csv (Linhas: 1)", "duracao_ms": 388, "arquivo_csv": "C:\\xampp\\htdocs\\DMC-DATALOAD\\storage\\logs\\execucao_4_21_1774541800.csv"}, {"sql": "SELECT SETVAL(''sch_sad.sad_instituicao_codigo_seq'' , MAX(CODIGO)) FROM sch_sad.sad_instituicao;", "erro": null, "tipo": "SELECT", "bloco": "22", "ordem": 22, "status": "sucesso", "id_bloco": 578, "registros": 1, "resultado": "Arquivo gerado: C:\\xampp\\htdocs\\DMC-DATALOAD\\storage\\logs\\execucao_4_22_1774541801.csv (Linhas: 1)", "duracao_ms": 45, "arquivo_csv": "C:\\xampp\\htdocs\\DMC-DATALOAD\\storage\\logs\\execucao_4_22_1774541801.csv"}, {"sql": "select \n\tcount(u.id_usuario)\nfrom\n\tsch_sad.sad_servidor s\n\tjoin sch_sad.sad_vinculo v on s.codigo = v.codigo_sad_servidor\n\tjoin sch_sgic.sgic_usuario u on s.cpf = u.cpf\n\tjoin sch_sgic.sgic_escola e on e.ind_atende_todas_instituicoes = true\nwhere \n\ts.ind_ativo = ''1'';", "erro": null, "tipo": "SELECT", "bloco": "23", "ordem": 23, "status": "sucesso", "id_bloco": 579, "registros": 1, "resultado": "Arquivo gerado: C:\\xampp\\htdocs\\DMC-DATALOAD\\storage\\logs\\execucao_4_23_1774541803.csv (Linhas: 1)", "duracao_ms": 2159, "arquivo_csv": "C:\\xampp\\htdocs\\DMC-DATALOAD\\storage\\logs\\execucao_4_23_1774541803.csv"}, {"sql": "delete from sch_sgic.sgic_usuario_escola\nwhere id_usuario in (\n  select u.id_usuario\n  from sch_sad.sad_servidor s\n  join sch_sad.sad_vinculo v on s.codigo = v.codigo_sad_servidor\n  join sch_sgic.sgic_usuario u on s.cpf = u.cpf\n  join sch_sgic.sgic_escola e on e.ind_atende_todas_instituicoes = true\n  where s.ind_ativo = ''1''\n);", "erro": null, "tipo": "SELECT", "bloco": "24", "ordem": 24, "status": "sucesso", "id_bloco": 580, "registros": 533652, "resultado": "Linhas afetadas: 533652", "duracao_ms": 4503, "arquivo_csv": null}, {"sql": "insert into sch_sgic.sgic_usuario_escola (id_usuario, ind_ativo, matricula, codigo_instituicao, id_escola, id_outras_entidades)\nselect distinct u.id_usuario\n      , CASE WHEN v.ind_ativo = ''1'' THEN true else false end ind_ativo\n      , LPAD(v.matricula, 11, ''0'')\n      , v.codigo_sad_instituicao\n      , e.id_escola\n      , 0 id_outras_entidades\nfrom \n\tsch_sad.sad_servidor s\n\tjoin sch_sad.sad_vinculo v on s.codigo = v.codigo_sad_servidor\n\tjoin sch_sgic.sgic_usuario u on s.cpf = u.cpf\n\tjoin sch_sgic.sgic_escola e on e.ind_atende_todas_instituicoes = true\nwhere\n\ts.ind_ativo = ''1'';", "erro": null, "tipo": "INSERT", "bloco": "25", "ordem": 25, "status": "sucesso", "id_bloco": 581, "registros": 533652, "resultado": "Linhas afetadas: 533652", "duracao_ms": 14892, "arquivo_csv": null}, {"sql": "SELECT sch_sad.fn_excluir_tab_imp();", "erro": null, "tipo": "SELECT", "bloco": "26", "ordem": 26, "status": "sucesso", "id_bloco": 582, "registros": 1, "resultado": "Arquivo gerado: C:\\xampp\\htdocs\\DMC-DATALOAD\\storage\\logs\\execucao_4_26_1774541823.csv (Linhas: 1)", "duracao_ms": 559, "arquivo_csv": "C:\\xampp\\htdocs\\DMC-DATALOAD\\storage\\logs\\execucao_4_26_1774541823.csv"}], "opcoes": {"interrompido": false, "parar_em_erro": true, "iniciar_de_bloco": null, "rollback_em_erro": false, "rollback_realizado": false, "blocos_selecionados": null}}', NULL, 79916, 26, 26, 0, NULL, NULL, 2001691);


--
-- TOC entry 5507 (class 0 OID 45632)
-- Dependencies: 236
-- Data for Name: tb_logs_sistema; Type: TABLE DATA; Schema: public; Owner: postgres
--



--
-- TOC entry 5509 (class 0 OID 45647)
-- Dependencies: 238
-- Data for Name: tb_metricas_sistema; Type: TABLE DATA; Schema: public; Owner: postgres
--



--
-- TOC entry 5535 (class 0 OID 46308)
-- Dependencies: 265
-- Data for Name: tb_notificacoes; Type: TABLE DATA; Schema: public; Owner: postgres
--



--
-- TOC entry 5564 (class 0 OID 46602)
-- Dependencies: 294
-- Data for Name: tb_password_resets; Type: TABLE DATA; Schema: public; Owner: postgres
--

INSERT INTO public.tb_password_resets OVERRIDING SYSTEM VALUE VALUES (4, 3, 'f9f23c6e1630bf43554a6081a3f7f22c07ec00fe5b26ca373967a5eaa6919315', '333A8E', '2026-03-24 09:25:07.600662-03', '2026-03-24 09:55:07.600662-03', true);
INSERT INTO public.tb_password_resets OVERRIDING SYSTEM VALUE VALUES (6, 32, '077b93c2991925bc04411ca74c29188ad0ab56690e0e04c519bca0b6608d2b2c', '31A81F', '2026-03-24 10:24:24.031253-03', '2026-03-24 10:54:24.031253-03', true);
INSERT INTO public.tb_password_resets OVERRIDING SYSTEM VALUE VALUES (1, 112, 'd9a580d4da2e6e40ecf0fd46f843585ca15ee843178287a7562ef6e897d3fd61', 'A53D30', '2026-03-26 12:55:48.46156-03', '2026-03-26 13:25:48.46156-03', true);


--
-- TOC entry 5499 (class 0 OID 45561)
-- Dependencies: 228
-- Data for Name: tb_perfis_conexao; Type: TABLE DATA; Schema: public; Owner: postgres
--

INSERT INTO public.tb_perfis_conexao OVERRIDING SYSTEM VALUE VALUES (2, 'SGIC - HOMOLOGAÇÃO', 'postgres', '200.238.107.57', 5432, 'sadsiswebhom_new', 'saduseradm', 'Kx7BkB7MSMJBr5sIJC0Wnw==:eUwLAsesoaxezShEaIvEzw==', '[]', '2026-03-25 09:49:32.488806-03', NULL);
INSERT INTO public.tb_perfis_conexao OVERRIDING SYSTEM VALUE VALUES (4, 'SGIC - PRODUÇÃO', 'postgres', '10.238.107.168', 5432, 'sadsiswebpro', 'saduseradm', '9BrcrMHZBARoju/nNYejGA==:srdSdTvriJoHg9EQdTfyjYJ+YFNTnTvy350d6iZCBGM=', '[]', '2026-03-25 14:56:39.357073-03', NULL);
INSERT INTO public.tb_perfis_conexao OVERRIDING SYSTEM VALUE VALUES (5, 'SGD - HOMOLOGAÇÃO', 'postgres', '200.238.107.125', 5432, 'sadsiswebhom', 'saduseradm', 'wBxhqdwUEYXUeHFjhTKwrA==:5C398thgZynGWlN0/7kxxQ==', '[]', '2026-03-25 15:32:36.339722-03', NULL);
INSERT INTO public.tb_perfis_conexao OVERRIDING SYSTEM VALUE VALUES (6, 'SGD - PRODUÇÃO', 'postgres', '200.238.107.93', 5432, 'sadsiswebpro', 'saduseradm', 'FGruHfCJo5psA5ugoO8WZA==:RrZohI6RD6VqE/IQ9TblTwGVgt2Su5np9YTYAsSeFtI=', '[]', '2026-03-25 15:34:44.046293-03', NULL);
INSERT INTO public.tb_perfis_conexao OVERRIDING SYSTEM VALUE VALUES (8, 'SGP | HOMOLOGAÇÃO - C_ERGON', 'oracle', '10.238.205.116', 1521, NULL, 'c_ergon', 'vjilBU+zZngA+4f6JipWoQ==:DPYQMx8erwndFv8slBzEcQ==', '{"sid": "SADRHPRO", "tipo_conexao_oracle": "sid"}', '2026-03-25 15:38:38.757406-03', NULL);
INSERT INTO public.tb_perfis_conexao OVERRIDING SYSTEM VALUE VALUES (7, 'SGP | TREINAMENTO - C_ERGON', 'oracle', '10.238.75.224', 1521, NULL, 'c_ergon', 'e+rF11siac6CNUSMscg3bA==:+ebNiMN42xxXYJVwJnOyKA==', '{"sid": "SADRHPRO", "tipo_conexao_oracle": "sid"}', '2026-03-25 15:35:55.758825-03', NULL);
INSERT INTO public.tb_perfis_conexao OVERRIDING SYSTEM VALUE VALUES (1, 'SQLLITE-TESTE', 'sqlite', '', NULL, 'C:\Users\caio.barros\Downloads\data.sqllite', NULL, '/4ZSWElXUY08+L8e2NTCzg==:JIL6+qACrEumnJO4PI/RHQ==', '{"sqlite_path": "C:\\Users\\caio.barros\\Downloads\\data.sqllite"}', '2026-03-25 09:42:00.686617-03', NULL);


--
-- TOC entry 5533 (class 0 OID 46278)
-- Dependencies: 263
-- Data for Name: tb_pipeline_execucoes; Type: TABLE DATA; Schema: public; Owner: postgres
--

INSERT INTO public.tb_pipeline_execucoes VALUES (4, 36, 'success', '2026-03-26 13:15:43.829673-03', '2026-03-26 13:17:06.568677-03', 82736, 6, 6, 5, 0, '{"1": {"started": true, "timestamp": "2026-03-26T17:15:43+01:00"}, "2": {"status": "sucesso", "sucesso": true, "mensagem": "", "rotina_id": 4, "duracao_ms": 79916, "blocos_falha": 0, "blocos_sucesso": 26, "registros_total": 2001691, "blocos_executados": 26}, "5": {"to": "renan.batista@sad.pe.gov.br", "sent": true, "subject": "CARGA | MENSAL: 3 - Sucesso", "attachments": 0}, "8": {"left": true, "right": true, "condition": true}, "9": {"finished": true}}', '[{"type": "trigger", "label": "Trigger", "status": "success", "node_id": 1, "timestamp": "2026-03-26T17:15:43+01:00", "duration_ms": 0, "result_preview": "2 itens"}, {"type": "rotina", "label": "Rotina", "status": "success", "node_id": "2", "timestamp": "2026-03-26T17:17:03+01:00", "duration_ms": 79926, "result_preview": "9 itens"}, {"type": "condition", "label": "Condition", "status": "success", "node_id": "8", "timestamp": "2026-03-26T17:17:03+01:00", "duration_ms": 0, "result_preview": "3 itens"}, {"type": "email", "label": "Email", "status": "success", "node_id": "5", "timestamp": "2026-03-26T17:17:06+01:00", "duration_ms": 2810, "result_preview": "4 itens"}, {"type": "email", "label": "Email", "status": "skipped", "message": "Pulado por condição", "node_id": "6", "timestamp": "2026-03-26T17:17:06+01:00"}, {"type": "end", "label": "End", "status": "success", "node_id": "9", "timestamp": "2026-03-26T17:17:06+01:00", "duration_ms": 0, "result_preview": "1 itens"}]', NULL, 112);


--
-- TOC entry 5531 (class 0 OID 46258)
-- Dependencies: 261
-- Data for Name: tb_pipelines; Type: TABLE DATA; Schema: public; Owner: postgres
--

INSERT INTO public.tb_pipelines VALUES (1, 'CARGA | MENSAL: 3 | HOMOLOGAÇÃO', '', 'nocode', false, '{"drawflow": {"Home": {"data": {"1": {"id": 1, "data": {"type": "trigger", "label": "Trigger"}, "html": "<div class=\"df-node-container\" data-type=\"trigger\"><div class=\"df-node-header\" style=\"background: #10b98111;\"><div class=\"df-node-icon\" style=\"background:#10b981\"><i class=\"bi bi-play-circle\"></i></div><div><div class=\"df-node-title\">Trigger</div><div class=\"df-node-subtitle\">Manual</div></div></div></div>", "name": "trigger", "class": "trigger", "pos_x": 68, "pos_y": 41, "inputs": {}, "outputs": {"output_1": {"connections": [{"node": "2", "output": "input_1"}]}}, "typenode": false}, "2": {"id": 2, "data": {"type": "rotina", "label": "Rotina", "rotina_id": "1", "stop_on_error": "false", "output_variable": "resultado"}, "html": "<div class=\"df-node-container\" data-type=\"rotina\"><div class=\"df-node-header\" style=\"background: #3b82f611;\"><div class=\"df-node-icon\" style=\"background:#3b82f6\"><i class=\"bi bi-gear-wide-connected\"></i></div><div><div class=\"df-node-title\">Rotina</div><div class=\"df-node-subtitle\">Selecionar rotina</div></div></div><div class=\"df-node-body\"><span class=\"text-muted fst-italic\">Selecionar rotina</span></div></div>", "name": "rotina", "class": "rotina", "pos_x": 218, "pos_y": 202, "inputs": {"input_1": {"connections": [{"node": "1", "input": "output_1"}]}}, "outputs": {"output_1": {"connections": [{"node": "8", "output": "input_1"}]}}, "typenode": false}, "5": {"id": 5, "data": {"type": "email", "label": "Email", "email_to": "caio.barros@sad.pe.gov.br", "email_subject": "CARGA | MENSAL: 3 - Sucesso"}, "html": "<div class=\"df-node-container\" data-type=\"email\"><div class=\"df-node-header\" style=\"background: #ec489911;\"><div class=\"df-node-icon\" style=\"background:#ec4899\"><i class=\"bi bi-envelope\"></i></div><div><div class=\"df-node-title\">Email</div><div class=\"df-node-subtitle\">Configurar</div></div></div></div>", "name": "email", "class": "email", "pos_x": 718, "pos_y": 331, "inputs": {"input_1": {"connections": [{"node": "8", "input": "output_1"}]}}, "outputs": {"output_1": {"connections": [{"node": "9", "output": "input_1"}]}}, "typenode": false}, "6": {"id": 6, "data": {"type": "email", "label": "Email", "email_to": "caio.barros@sad.pe.gov.br", "email_subject": "CARGA | MENSAL: 3 - Falha"}, "html": "<div class=\"df-node-container\" data-type=\"email\"><div class=\"df-node-header\" style=\"background: #ec489911;\"><div class=\"df-node-icon\" style=\"background:#ec4899\"><i class=\"bi bi-envelope\"></i></div><div><div class=\"df-node-title\">Email</div><div class=\"df-node-subtitle\">Configurar</div></div></div></div>", "name": "email", "class": "email", "pos_x": 720, "pos_y": 512, "inputs": {"input_1": {"connections": [{"node": "8", "input": "output_2"}]}}, "outputs": {"output_1": {"connections": [{"node": "9", "output": "input_1"}]}}, "typenode": false}, "8": {"id": 8, "data": {"type": "condition", "label": "Condition", "operator": "==", "left_operand": "{{resultado.sucesso}}", "right_operand": "true"}, "html": "<div class=\"df-node-container\" data-type=\"condition\"><div class=\"df-node-header\" style=\"background: #eab30811;\"><div class=\"df-node-icon\" style=\"background:#eab308\"><i class=\"bi bi-signpost-split\"></i></div><div><div class=\"df-node-title\">Condition</div><div class=\"df-node-subtitle\">Configurar condição</div></div></div><div class=\"df-node-body\"><span class=\"text-muted fst-italic\">Configurar condição</span></div></div>", "name": "condition", "class": "condition", "pos_x": 418, "pos_y": 396, "inputs": {"input_1": {"connections": [{"node": "2", "input": "output_1"}]}}, "outputs": {"output_1": {"connections": [{"node": "5", "output": "input_1"}]}, "output_2": {"connections": [{"node": "6", "output": "input_1"}]}}, "typenode": false}, "9": {"id": 9, "data": {"type": "end", "label": "End"}, "html": "<div class=\"df-node-container\" data-type=\"end\"><div class=\"df-node-header\" style=\"background: #dc262611;\"><div class=\"df-node-icon\" style=\"background:#dc2626\"><i class=\"bi bi-stop-circle\"></i></div><div><div class=\"df-node-title\">End</div></div></div></div>", "name": "end", "class": "end", "pos_x": 999, "pos_y": 414, "inputs": {"input_1": {"connections": [{"node": "5", "input": "output_1"}, {"node": "6", "input": "output_1"}]}}, "outputs": {}, "typenode": false}}}}}', '{
  "drawflow": {
    "Home": {
      "data": {
        "1": {
          "id": 1,
          "data": {
            "type": "trigger",
            "label": "Trigger"
          },
          "html": "<div class=\"df-node-container\" data-type=\"trigger\"><div class=\"df-node-header\" style=\"background: #10b98111;\"><div class=\"df-node-icon\" style=\"background:#10b981\"><i class=\"bi bi-play-circle\"></i></div><div><div class=\"df-node-title\">Trigger</div><div class=\"df-node-subtitle\">Manual</div></div></div></div>",
          "name": "trigger",
          "class": "trigger",
          "pos_x": 68,
          "pos_y": 41,
          "inputs": {},
          "outputs": {
            "output_1": {
              "connections": [
                {
                  "node": "2",
                  "output": "input_1"
                }
              ]
            }
          },
          "typenode": false
        },
        "2": {
          "id": 2,
          "data": {
            "type": "rotina",
            "label": "Rotina",
            "rotina_id": "1",
            "stop_on_error": "false",
            "output_variable": "resultado"
          },
          "html": "<div class=\"df-node-container\" data-type=\"rotina\"><div class=\"df-node-header\" style=\"background: #3b82f611;\"><div class=\"df-node-icon\" style=\"background:#3b82f6\"><i class=\"bi bi-gear-wide-connected\"></i></div><div><div class=\"df-node-title\">Rotina</div><div class=\"df-node-subtitle\">Selecionar rotina</div></div></div><div class=\"df-node-body\"><span class=\"text-muted fst-italic\">Selecionar rotina</span></div></div>",
          "name": "rotina",
          "class": "rotina",
          "pos_x": 218,
          "pos_y": 202,
          "inputs": {
            "input_1": {
              "connections": [
                {
                  "node": "1",
                  "input": "output_1"
                }
              ]
            }
          },
          "outputs": {
            "output_1": {
              "connections": [
                {
                  "node": "8",
                  "output": "input_1"
                }
              ]
            }
          },
          "typenode": false
        },
        "5": {
          "id": 5,
          "data": {
            "type": "email",
            "label": "Email",
            "email_to": "caio.barros@sad.pe.gov.br",
            "email_subject": "CARGA | MENSAL: 3 - Sucesso"
          },
          "html": "<div class=\"df-node-container\" data-type=\"email\"><div class=\"df-node-header\" style=\"background: #ec489911;\"><div class=\"df-node-icon\" style=\"background:#ec4899\"><i class=\"bi bi-envelope\"></i></div><div><div class=\"df-node-title\">Email</div><div class=\"df-node-subtitle\">Configurar</div></div></div></div>",
          "name": "email",
          "class": "email",
          "pos_x": 718,
          "pos_y": 331,
          "inputs": {
            "input_1": {
              "connections": [
                {
                  "node": "8",
                  "input": "output_1"
                }
              ]
            }
          },
          "outputs": {
            "output_1": {
              "connections": [
                {
                  "node": "9",
                  "output": "input_1"
                }
              ]
            }
          },
          "typenode": false
        },
        "6": {
          "id": 6,
          "data": {
            "type": "email",
            "label": "Email",
            "email_to": "caio.barros@sad.pe.gov.br",
            "email_subject": "CARGA | MENSAL: 3 - Falha"
          },
          "html": "<div class=\"df-node-container\" data-type=\"email\"><div class=\"df-node-header\" style=\"background: #ec489911;\"><div class=\"df-node-icon\" style=\"background:#ec4899\"><i class=\"bi bi-envelope\"></i></div><div><div class=\"df-node-title\">Email</div><div class=\"df-node-subtitle\">Configurar</div></div></div></div>",
          "name": "email",
          "class": "email",
          "pos_x": 720,
          "pos_y": 512,
          "inputs": {
            "input_1": {
              "connections": [
                {
                  "node": "8",
                  "input": "output_2"
                }
              ]
            }
          },
          "outputs": {
            "output_1": {
              "connections": [
                {
                  "node": "9",
                  "output": "input_1"
                }
              ]
            }
          },
          "typenode": false
        },
        "8": {
          "id": 8,
          "data": {
            "type": "condition",
            "label": "Condition",
            "operator": "==",
            "left_operand": "{{resultado.sucesso}}",
            "right_operand": "true"
          },
          "html": "<div class=\"df-node-container\" data-type=\"condition\"><div class=\"df-node-header\" style=\"background: #eab30811;\"><div class=\"df-node-icon\" style=\"background:#eab308\"><i class=\"bi bi-signpost-split\"></i></div><div><div class=\"df-node-title\">Condition</div><div class=\"df-node-subtitle\">Configurar condição</div></div></div><div class=\"df-node-body\"><span class=\"text-muted fst-italic\">Configurar condição</span></div></div>",
          "name": "condition",
          "class": "condition",
          "pos_x": 418,
          "pos_y": 396,
          "inputs": {
            "input_1": {
              "connections": [
                {
                  "node": "2",
                  "input": "output_1"
                }
              ]
            }
          },
          "outputs": {
            "output_1": {
              "connections": [
                {
                  "node": "5",
                  "output": "input_1"
                }
              ]
            },
            "output_2": {
              "connections": [
                {
                  "node": "6",
                  "output": "input_1"
                }
              ]
            }
          },
          "typenode": false
        },
        "9": {
          "id": 9,
          "data": {
            "type": "end",
            "label": "End"
          },
          "html": "<div class=\"df-node-container\" data-type=\"end\"><div class=\"df-node-header\" style=\"background: #dc262611;\"><div class=\"df-node-icon\" style=\"background:#dc2626\"><i class=\"bi bi-stop-circle\"></i></div><div><div class=\"df-node-title\">End</div></div></div></div>",
          "name": "end",
          "class": "end",
          "pos_x": 999,
          "pos_y": 414,
          "inputs": {
            "input_1": {
              "connections": [
                {
                  "node": "5",
                  "input": "output_1"
                },
                {
                  "node": "6",
                  "input": "output_1"
                }
              ]
            }
          },
          "outputs": {},
          "typenode": false
        }
      }
    }
  }
}', '{}', '', 'manual', '{"trigger_type": "manual"}', 10, '[]', 1, '2026-03-25 10:42:11.627803-03', '2026-03-25 15:26:18.295308-03');
INSERT INTO public.tb_pipelines VALUES (36, 'CARGA | MENSAL: 3 | PRODUÇÃO', '', 'nocode', false, '{"drawflow": {"Home": {"data": {"1": {"id": 1, "data": {"type": "trigger", "label": "Trigger"}, "html": "<div class=\"df-node-container\" data-type=\"trigger\"><div class=\"df-node-header\" style=\"background: #10b98111;\"><div class=\"df-node-icon\" style=\"background:#10b981\"><i class=\"bi bi-play-circle\"></i></div><div><div class=\"df-node-title\">Trigger</div><div class=\"df-node-subtitle\">Manual</div></div></div></div>", "name": "trigger", "class": "trigger", "pos_x": 68, "pos_y": 41, "inputs": {}, "outputs": {"output_1": {"connections": [{"node": "2", "output": "input_1"}]}}, "typenode": false}, "2": {"id": 2, "data": {"type": "rotina", "label": "Rotina", "rotina_id": "4", "stop_on_error": "false", "output_variable": "resultado"}, "html": "<div class=\"df-node-container\" data-type=\"rotina\"><div class=\"df-node-header\" style=\"background: #3b82f611;\"><div class=\"df-node-icon\" style=\"background:#3b82f6\"><i class=\"bi bi-gear-wide-connected\"></i></div><div><div class=\"df-node-title\">Rotina</div><div class=\"df-node-subtitle\">Selecionar rotina</div></div></div><div class=\"df-node-body\"><span class=\"text-muted fst-italic\">Selecionar rotina</span></div></div>", "name": "rotina", "class": "rotina", "pos_x": 218, "pos_y": 202, "inputs": {"input_1": {"connections": [{"node": "1", "input": "output_1"}]}}, "outputs": {"output_1": {"connections": [{"node": "8", "output": "input_1"}]}}, "typenode": false}, "5": {"id": 5, "data": {"type": "email", "label": "Email", "email_to": "renan.batista@sad.pe.gov.br", "email_subject": "CARGA | MENSAL: 3 - Sucesso"}, "html": "<div class=\"df-node-container\" data-type=\"email\"><div class=\"df-node-header\" style=\"background: #ec489911;\"><div class=\"df-node-icon\" style=\"background:#ec4899\"><i class=\"bi bi-envelope\"></i></div><div><div class=\"df-node-title\">Email</div><div class=\"df-node-subtitle\">Configurar</div></div></div></div>", "name": "email", "class": "email", "pos_x": 718, "pos_y": 331, "inputs": {"input_1": {"connections": [{"node": "8", "input": "output_1"}]}}, "outputs": {"output_1": {"connections": [{"node": "9", "output": "input_1"}]}}, "typenode": false}, "6": {"id": 6, "data": {"type": "email", "label": "Email", "email_to": "renan.batista@sad.pe.gov.br", "email_subject": "CARGA | MENSAL: 3 - Falha"}, "html": "<div class=\"df-node-container\" data-type=\"email\"><div class=\"df-node-header\" style=\"background: #ec489911;\"><div class=\"df-node-icon\" style=\"background:#ec4899\"><i class=\"bi bi-envelope\"></i></div><div><div class=\"df-node-title\">Email</div><div class=\"df-node-subtitle\">Configurar</div></div></div></div>", "name": "email", "class": "email", "pos_x": 720, "pos_y": 512, "inputs": {"input_1": {"connections": [{"node": "8", "input": "output_2"}]}}, "outputs": {"output_1": {"connections": [{"node": "9", "output": "input_1"}]}}, "typenode": false}, "8": {"id": 8, "data": {"type": "condition", "label": "Condition", "operator": "==", "left_operand": "{{resultado.sucesso}}", "right_operand": "true"}, "html": "<div class=\"df-node-container\" data-type=\"condition\"><div class=\"df-node-header\" style=\"background: #eab30811;\"><div class=\"df-node-icon\" style=\"background:#eab308\"><i class=\"bi bi-signpost-split\"></i></div><div><div class=\"df-node-title\">Condition</div><div class=\"df-node-subtitle\">Configurar condição</div></div></div><div class=\"df-node-body\"><span class=\"text-muted fst-italic\">Configurar condição</span></div></div>", "name": "condition", "class": "condition", "pos_x": 418, "pos_y": 396, "inputs": {"input_1": {"connections": [{"node": "2", "input": "output_1"}]}}, "outputs": {"output_1": {"connections": [{"node": "5", "output": "input_1"}]}, "output_2": {"connections": [{"node": "6", "output": "input_1"}]}}, "typenode": false}, "9": {"id": 9, "data": {"type": "end", "label": "End"}, "html": "<div class=\"df-node-container\" data-type=\"end\"><div class=\"df-node-header\" style=\"background: #dc262611;\"><div class=\"df-node-icon\" style=\"background:#dc2626\"><i class=\"bi bi-stop-circle\"></i></div><div><div class=\"df-node-title\">End</div></div></div></div>", "name": "end", "class": "end", "pos_x": 999, "pos_y": 414, "inputs": {"input_1": {"connections": [{"node": "5", "input": "output_1"}, {"node": "6", "input": "output_1"}]}}, "outputs": {}, "typenode": false}}}}}', '{
  "drawflow": {
    "Home": {
      "data": {
        "1": {
          "id": 1,
          "data": {
            "type": "trigger",
            "label": "Trigger"
          },
          "html": "<div class=\"df-node-container\" data-type=\"trigger\"><div class=\"df-node-header\" style=\"background: #10b98111;\"><div class=\"df-node-icon\" style=\"background:#10b981\"><i class=\"bi bi-play-circle\"></i></div><div><div class=\"df-node-title\">Trigger</div><div class=\"df-node-subtitle\">Manual</div></div></div></div>",
          "name": "trigger",
          "class": "trigger",
          "pos_x": 68,
          "pos_y": 41,
          "inputs": {},
          "outputs": {
            "output_1": {
              "connections": [
                {
                  "node": "2",
                  "output": "input_1"
                }
              ]
            }
          },
          "typenode": false
        },
        "2": {
          "id": 2,
          "data": {
            "type": "rotina",
            "label": "Rotina",
            "rotina_id": "4",
            "stop_on_error": "false",
            "output_variable": "resultado"
          },
          "html": "<div class=\"df-node-container\" data-type=\"rotina\"><div class=\"df-node-header\" style=\"background: #3b82f611;\"><div class=\"df-node-icon\" style=\"background:#3b82f6\"><i class=\"bi bi-gear-wide-connected\"></i></div><div><div class=\"df-node-title\">Rotina</div><div class=\"df-node-subtitle\">Selecionar rotina</div></div></div><div class=\"df-node-body\"><span class=\"text-muted fst-italic\">Selecionar rotina</span></div></div>",
          "name": "rotina",
          "class": "rotina",
          "pos_x": 218,
          "pos_y": 202,
          "inputs": {
            "input_1": {
              "connections": [
                {
                  "node": "1",
                  "input": "output_1"
                }
              ]
            }
          },
          "outputs": {
            "output_1": {
              "connections": [
                {
                  "node": "8",
                  "output": "input_1"
                }
              ]
            }
          },
          "typenode": false
        },
        "5": {
          "id": 5,
          "data": {
            "type": "email",
            "label": "Email",
            "email_to": "renan.batista@sad.pe.gov.br",
            "email_subject": "CARGA | MENSAL: 3 - Sucesso"
          },
          "html": "<div class=\"df-node-container\" data-type=\"email\"><div class=\"df-node-header\" style=\"background: #ec489911;\"><div class=\"df-node-icon\" style=\"background:#ec4899\"><i class=\"bi bi-envelope\"></i></div><div><div class=\"df-node-title\">Email</div><div class=\"df-node-subtitle\">Configurar</div></div></div></div>",
          "name": "email",
          "class": "email",
          "pos_x": 718,
          "pos_y": 331,
          "inputs": {
            "input_1": {
              "connections": [
                {
                  "node": "8",
                  "input": "output_1"
                }
              ]
            }
          },
          "outputs": {
            "output_1": {
              "connections": [
                {
                  "node": "9",
                  "output": "input_1"
                }
              ]
            }
          },
          "typenode": false
        },
        "6": {
          "id": 6,
          "data": {
            "type": "email",
            "label": "Email",
            "email_to": "renan.batista@sad.pe.gov.br",
            "email_subject": "CARGA | MENSAL: 3 - Falha"
          },
          "html": "<div class=\"df-node-container\" data-type=\"email\"><div class=\"df-node-header\" style=\"background: #ec489911;\"><div class=\"df-node-icon\" style=\"background:#ec4899\"><i class=\"bi bi-envelope\"></i></div><div><div class=\"df-node-title\">Email</div><div class=\"df-node-subtitle\">Configurar</div></div></div></div>",
          "name": "email",
          "class": "email",
          "pos_x": 720,
          "pos_y": 512,
          "inputs": {
            "input_1": {
              "connections": [
                {
                  "node": "8",
                  "input": "output_2"
                }
              ]
            }
          },
          "outputs": {
            "output_1": {
              "connections": [
                {
                  "node": "9",
                  "output": "input_1"
                }
              ]
            }
          },
          "typenode": false
        },
        "8": {
          "id": 8,
          "data": {
            "type": "condition",
            "label": "Condition",
            "operator": "==",
            "left_operand": "{{resultado.sucesso}}",
            "right_operand": "true"
          },
          "html": "<div class=\"df-node-container\" data-type=\"condition\"><div class=\"df-node-header\" style=\"background: #eab30811;\"><div class=\"df-node-icon\" style=\"background:#eab308\"><i class=\"bi bi-signpost-split\"></i></div><div><div class=\"df-node-title\">Condition</div><div class=\"df-node-subtitle\">Configurar condição</div></div></div><div class=\"df-node-body\"><span class=\"text-muted fst-italic\">Configurar condição</span></div></div>",
          "name": "condition",
          "class": "condition",
          "pos_x": 418,
          "pos_y": 396,
          "inputs": {
            "input_1": {
              "connections": [
                {
                  "node": "2",
                  "input": "output_1"
                }
              ]
            }
          },
          "outputs": {
            "output_1": {
              "connections": [
                {
                  "node": "5",
                  "output": "input_1"
                }
              ]
            },
            "output_2": {
              "connections": [
                {
                  "node": "6",
                  "output": "input_1"
                }
              ]
            }
          },
          "typenode": false
        },
        "9": {
          "id": 9,
          "data": {
            "type": "end",
            "label": "End"
          },
          "html": "<div class=\"df-node-container\" data-type=\"end\"><div class=\"df-node-header\" style=\"background: #dc262611;\"><div class=\"df-node-icon\" style=\"background:#dc2626\"><i class=\"bi bi-stop-circle\"></i></div><div><div class=\"df-node-title\">End</div></div></div></div>",
          "name": "end",
          "class": "end",
          "pos_x": 999,
          "pos_y": 414,
          "inputs": {
            "input_1": {
              "connections": [
                {
                  "node": "5",
                  "input": "output_1"
                },
                {
                  "node": "6",
                  "input": "output_1"
                }
              ]
            }
          },
          "outputs": {},
          "typenode": false
        }
      }
    }
  }
}', '{}', '', 'manual', '{"trigger_type": "manual"}', 2, '[]', 1, '2026-03-26 11:12:40.109644-03', '2026-03-26 11:13:10.342643-03');
INSERT INTO public.tb_pipelines VALUES (34, 'CARGA | MENSAL: 4 | HOMOLOGAÇÃO', '', 'nocode', false, '{"drawflow": {"Home": {"data": {"1": {"id": 1, "data": {"type": "trigger", "label": "Trigger"}, "html": "<div class=\"df-node-container\" data-type=\"trigger\"><div class=\"df-node-header\" style=\"background: #10b98111;\"><div class=\"df-node-icon\" style=\"background:#10b981\"><i class=\"bi bi-play-circle\"></i></div><div><div class=\"df-node-title\">Trigger</div><div class=\"df-node-subtitle\">Manual</div></div></div></div>", "name": "trigger", "class": "trigger", "pos_x": 68, "pos_y": 41, "inputs": {}, "outputs": {"output_1": {"connections": [{"node": "2", "output": "input_1"}]}}, "typenode": false}, "2": {"id": 2, "data": {"type": "rotina", "label": "Rotina", "rotina_id": "2", "stop_on_error": "false", "output_variable": "resultado"}, "html": "<div class=\"df-node-container\" data-type=\"rotina\"><div class=\"df-node-header\" style=\"background: #3b82f611;\"><div class=\"df-node-icon\" style=\"background:#3b82f6\"><i class=\"bi bi-gear-wide-connected\"></i></div><div><div class=\"df-node-title\">Rotina</div><div class=\"df-node-subtitle\">Selecionar rotina</div></div></div><div class=\"df-node-body\"><span class=\"text-muted fst-italic\">Selecionar rotina</span></div></div>", "name": "rotina", "class": "rotina", "pos_x": 218, "pos_y": 202, "inputs": {"input_1": {"connections": [{"node": "1", "input": "output_1"}]}}, "outputs": {"output_1": {"connections": [{"node": "8", "output": "input_1"}]}}, "typenode": false}, "5": {"id": 5, "data": {"type": "email", "label": "Email", "email_to": "caio.barros@sad.pe.gov.br", "email_subject": "CARGA | MENSAL: 4 - Sucesso"}, "html": "<div class=\"df-node-container\" data-type=\"email\"><div class=\"df-node-header\" style=\"background: #ec489911;\"><div class=\"df-node-icon\" style=\"background:#ec4899\"><i class=\"bi bi-envelope\"></i></div><div><div class=\"df-node-title\">Email</div><div class=\"df-node-subtitle\">Configurar</div></div></div></div>", "name": "email", "class": "email", "pos_x": 723, "pos_y": 323, "inputs": {"input_1": {"connections": [{"node": "8", "input": "output_1"}]}}, "outputs": {"output_1": {"connections": [{"node": "9", "output": "input_1"}]}}, "typenode": false}, "6": {"id": 6, "data": {"type": "email", "label": "Email", "email_to": "caio.barros@sad.pe.gov.br", "email_subject": "CARGA | MENSAL: 4 - Falha"}, "html": "<div class=\"df-node-container\" data-type=\"email\"><div class=\"df-node-header\" style=\"background: #ec489911;\"><div class=\"df-node-icon\" style=\"background:#ec4899\"><i class=\"bi bi-envelope\"></i></div><div><div class=\"df-node-title\">Email</div><div class=\"df-node-subtitle\">Configurar</div></div></div></div>", "name": "email", "class": "email", "pos_x": 720, "pos_y": 512, "inputs": {"input_1": {"connections": [{"node": "8", "input": "output_2"}]}}, "outputs": {"output_1": {"connections": [{"node": "9", "output": "input_1"}]}}, "typenode": false}, "8": {"id": 8, "data": {"type": "condition", "label": "Condition", "operator": "==", "left_operand": "{{resultado.sucesso}}", "right_operand": "true"}, "html": "<div class=\"df-node-container\" data-type=\"condition\"><div class=\"df-node-header\" style=\"background: #eab30811;\"><div class=\"df-node-icon\" style=\"background:#eab308\"><i class=\"bi bi-signpost-split\"></i></div><div><div class=\"df-node-title\">Condition</div><div class=\"df-node-subtitle\">Configurar condição</div></div></div><div class=\"df-node-body\"><span class=\"text-muted fst-italic\">Configurar condição</span></div></div>", "name": "condition", "class": "condition", "pos_x": 418, "pos_y": 396, "inputs": {"input_1": {"connections": [{"node": "2", "input": "output_1"}]}}, "outputs": {"output_1": {"connections": [{"node": "5", "output": "input_1"}]}, "output_2": {"connections": [{"node": "6", "output": "input_1"}]}}, "typenode": false}, "9": {"id": 9, "data": {"type": "end", "label": "End"}, "html": "<div class=\"df-node-container\" data-type=\"end\"><div class=\"df-node-header\" style=\"background: #dc262611;\"><div class=\"df-node-icon\" style=\"background:#dc2626\"><i class=\"bi bi-stop-circle\"></i></div><div><div class=\"df-node-title\">End</div></div></div></div>", "name": "end", "class": "end", "pos_x": 999, "pos_y": 414, "inputs": {"input_1": {"connections": [{"node": "5", "input": "output_1"}, {"node": "6", "input": "output_1"}]}}, "outputs": {}, "typenode": false}}}}}', '{
  "drawflow": {
    "Home": {
      "data": {
        "1": {
          "id": 1,
          "data": {
            "type": "trigger",
            "label": "Trigger"
          },
          "html": "<div class=\"df-node-container\" data-type=\"trigger\"><div class=\"df-node-header\" style=\"background: #10b98111;\"><div class=\"df-node-icon\" style=\"background:#10b981\"><i class=\"bi bi-play-circle\"></i></div><div><div class=\"df-node-title\">Trigger</div><div class=\"df-node-subtitle\">Manual</div></div></div></div>",
          "name": "trigger",
          "class": "trigger",
          "pos_x": 68,
          "pos_y": 41,
          "inputs": {},
          "outputs": {
            "output_1": {
              "connections": [
                {
                  "node": "2",
                  "output": "input_1"
                }
              ]
            }
          },
          "typenode": false
        },
        "2": {
          "id": 2,
          "data": {
            "type": "rotina",
            "label": "Rotina",
            "rotina_id": "2",
            "stop_on_error": "false",
            "output_variable": "resultado"
          },
          "html": "<div class=\"df-node-container\" data-type=\"rotina\"><div class=\"df-node-header\" style=\"background: #3b82f611;\"><div class=\"df-node-icon\" style=\"background:#3b82f6\"><i class=\"bi bi-gear-wide-connected\"></i></div><div><div class=\"df-node-title\">Rotina</div><div class=\"df-node-subtitle\">Selecionar rotina</div></div></div><div class=\"df-node-body\"><span class=\"text-muted fst-italic\">Selecionar rotina</span></div></div>",
          "name": "rotina",
          "class": "rotina",
          "pos_x": 218,
          "pos_y": 202,
          "inputs": {
            "input_1": {
              "connections": [
                {
                  "node": "1",
                  "input": "output_1"
                }
              ]
            }
          },
          "outputs": {
            "output_1": {
              "connections": [
                {
                  "node": "8",
                  "output": "input_1"
                }
              ]
            }
          },
          "typenode": false
        },
        "5": {
          "id": 5,
          "data": {
            "type": "email",
            "label": "Email",
            "email_to": "caio.barros@sad.pe.gov.br",
            "email_subject": "CARGA | MENSAL: 4 - Sucesso"
          },
          "html": "<div class=\"df-node-container\" data-type=\"email\"><div class=\"df-node-header\" style=\"background: #ec489911;\"><div class=\"df-node-icon\" style=\"background:#ec4899\"><i class=\"bi bi-envelope\"></i></div><div><div class=\"df-node-title\">Email</div><div class=\"df-node-subtitle\">Configurar</div></div></div></div>",
          "name": "email",
          "class": "email",
          "pos_x": 723,
          "pos_y": 323,
          "inputs": {
            "input_1": {
              "connections": [
                {
                  "node": "8",
                  "input": "output_1"
                }
              ]
            }
          },
          "outputs": {
            "output_1": {
              "connections": [
                {
                  "node": "9",
                  "output": "input_1"
                }
              ]
            }
          },
          "typenode": false
        },
        "6": {
          "id": 6,
          "data": {
            "type": "email",
            "label": "Email",
            "email_to": "caio.barros@sad.pe.gov.br",
            "email_subject": "CARGA | MENSAL: 4 - Falha"
          },
          "html": "<div class=\"df-node-container\" data-type=\"email\"><div class=\"df-node-header\" style=\"background: #ec489911;\"><div class=\"df-node-icon\" style=\"background:#ec4899\"><i class=\"bi bi-envelope\"></i></div><div><div class=\"df-node-title\">Email</div><div class=\"df-node-subtitle\">Configurar</div></div></div></div>",
          "name": "email",
          "class": "email",
          "pos_x": 720,
          "pos_y": 512,
          "inputs": {
            "input_1": {
              "connections": [
                {
                  "node": "8",
                  "input": "output_2"
                }
              ]
            }
          },
          "outputs": {
            "output_1": {
              "connections": [
                {
                  "node": "9",
                  "output": "input_1"
                }
              ]
            }
          },
          "typenode": false
        },
        "8": {
          "id": 8,
          "data": {
            "type": "condition",
            "label": "Condition",
            "operator": "==",
            "left_operand": "{{resultado.sucesso}}",
            "right_operand": "true"
          },
          "html": "<div class=\"df-node-container\" data-type=\"condition\"><div class=\"df-node-header\" style=\"background: #eab30811;\"><div class=\"df-node-icon\" style=\"background:#eab308\"><i class=\"bi bi-signpost-split\"></i></div><div><div class=\"df-node-title\">Condition</div><div class=\"df-node-subtitle\">Configurar condição</div></div></div><div class=\"df-node-body\"><span class=\"text-muted fst-italic\">Configurar condição</span></div></div>",
          "name": "condition",
          "class": "condition",
          "pos_x": 418,
          "pos_y": 396,
          "inputs": {
            "input_1": {
              "connections": [
                {
                  "node": "2",
                  "input": "output_1"
                }
              ]
            }
          },
          "outputs": {
            "output_1": {
              "connections": [
                {
                  "node": "5",
                  "output": "input_1"
                }
              ]
            },
            "output_2": {
              "connections": [
                {
                  "node": "6",
                  "output": "input_1"
                }
              ]
            }
          },
          "typenode": false
        },
        "9": {
          "id": 9,
          "data": {
            "type": "end",
            "label": "End"
          },
          "html": "<div class=\"df-node-container\" data-type=\"end\"><div class=\"df-node-header\" style=\"background: #dc262611;\"><div class=\"df-node-icon\" style=\"background:#dc2626\"><i class=\"bi bi-stop-circle\"></i></div><div><div class=\"df-node-title\">End</div></div></div></div>",
          "name": "end",
          "class": "end",
          "pos_x": 999,
          "pos_y": 414,
          "inputs": {
            "input_1": {
              "connections": [
                {
                  "node": "5",
                  "input": "output_1"
                },
                {
                  "node": "6",
                  "input": "output_1"
                }
              ]
            }
          },
          "outputs": {},
          "typenode": false
        }
      }
    }
  }
}', '{}', '', 'manual', '{"trigger_type": "manual"}', 4, '[]', 1, '2026-03-25 14:40:37.072082-03', '2026-03-25 15:26:05.099037-03');
INSERT INTO public.tb_pipelines VALUES (35, 'CARGA | MENSAL: 4 | PRODUÇÃO', '', 'nocode', false, '{"drawflow": {"Home": {"data": {"1": {"id": 1, "data": {"type": "trigger", "label": "Trigger"}, "html": "<div class=\"df-node-container\" data-type=\"trigger\"><div class=\"df-node-header\" style=\"background: #10b98111;\"><div class=\"df-node-icon\" style=\"background:#10b981\"><i class=\"bi bi-play-circle\"></i></div><div><div class=\"df-node-title\">Trigger</div><div class=\"df-node-subtitle\">Manual</div></div></div></div>", "name": "trigger", "class": "trigger", "pos_x": 68, "pos_y": 41, "inputs": {}, "outputs": {"output_1": {"connections": [{"node": "2", "output": "input_1"}]}}, "typenode": false}, "2": {"id": 2, "data": {"type": "rotina", "label": "Rotina", "rotina_id": "5", "stop_on_error": "false", "output_variable": "resultado"}, "html": "<div class=\"df-node-container\" data-type=\"rotina\"><div class=\"df-node-header\" style=\"background: #3b82f611;\"><div class=\"df-node-icon\" style=\"background:#3b82f6\"><i class=\"bi bi-gear-wide-connected\"></i></div><div><div class=\"df-node-title\">Rotina</div><div class=\"df-node-subtitle\">Selecionar rotina</div></div></div><div class=\"df-node-body\"><span class=\"text-muted fst-italic\">Selecionar rotina</span></div></div>", "name": "rotina", "class": "rotina", "pos_x": 218, "pos_y": 202, "inputs": {"input_1": {"connections": [{"node": "1", "input": "output_1"}]}}, "outputs": {"output_1": {"connections": [{"node": "8", "output": "input_1"}]}}, "typenode": false}, "5": {"id": 5, "data": {"type": "email", "label": "Email", "email_to": "renan.batista@sad.pe.gov.br", "email_subject": "CARGA | MENSAL: 4 - Sucesso"}, "html": "<div class=\"df-node-container\" data-type=\"email\"><div class=\"df-node-header\" style=\"background: #ec489911;\"><div class=\"df-node-icon\" style=\"background:#ec4899\"><i class=\"bi bi-envelope\"></i></div><div><div class=\"df-node-title\">Email</div><div class=\"df-node-subtitle\">Configurar</div></div></div></div>", "name": "email", "class": "email", "pos_x": 723, "pos_y": 323, "inputs": {"input_1": {"connections": [{"node": "8", "input": "output_1"}]}}, "outputs": {"output_1": {"connections": [{"node": "9", "output": "input_1"}]}}, "typenode": false}, "6": {"id": 6, "data": {"type": "email", "label": "Email", "email_to": "renan.batista@sad.pe.gov.br", "email_subject": "CARGA | MENSAL: 4 - Falha"}, "html": "<div class=\"df-node-container\" data-type=\"email\"><div class=\"df-node-header\" style=\"background: #ec489911;\"><div class=\"df-node-icon\" style=\"background:#ec4899\"><i class=\"bi bi-envelope\"></i></div><div><div class=\"df-node-title\">Email</div><div class=\"df-node-subtitle\">Configurar</div></div></div></div>", "name": "email", "class": "email", "pos_x": 720, "pos_y": 512, "inputs": {"input_1": {"connections": [{"node": "8", "input": "output_2"}]}}, "outputs": {"output_1": {"connections": [{"node": "9", "output": "input_1"}]}}, "typenode": false}, "8": {"id": 8, "data": {"type": "condition", "label": "Condition", "operator": "==", "left_operand": "{{resultado.sucesso}}", "right_operand": "true"}, "html": "<div class=\"df-node-container\" data-type=\"condition\"><div class=\"df-node-header\" style=\"background: #eab30811;\"><div class=\"df-node-icon\" style=\"background:#eab308\"><i class=\"bi bi-signpost-split\"></i></div><div><div class=\"df-node-title\">Condition</div><div class=\"df-node-subtitle\">Configurar condição</div></div></div><div class=\"df-node-body\"><span class=\"text-muted fst-italic\">Configurar condição</span></div></div>", "name": "condition", "class": "condition", "pos_x": 418, "pos_y": 396, "inputs": {"input_1": {"connections": [{"node": "2", "input": "output_1"}]}}, "outputs": {"output_1": {"connections": [{"node": "5", "output": "input_1"}]}, "output_2": {"connections": [{"node": "6", "output": "input_1"}]}}, "typenode": false}, "9": {"id": 9, "data": {"type": "end", "label": "End"}, "html": "<div class=\"df-node-container\" data-type=\"end\"><div class=\"df-node-header\" style=\"background: #dc262611;\"><div class=\"df-node-icon\" style=\"background:#dc2626\"><i class=\"bi bi-stop-circle\"></i></div><div><div class=\"df-node-title\">End</div></div></div></div>", "name": "end", "class": "end", "pos_x": 999, "pos_y": 414, "inputs": {"input_1": {"connections": [{"node": "5", "input": "output_1"}, {"node": "6", "input": "output_1"}]}}, "outputs": {}, "typenode": false}}}}}', '{
  "drawflow": {
    "Home": {
      "data": {
        "1": {
          "id": 1,
          "data": {
            "type": "trigger",
            "label": "Trigger"
          },
          "html": "<div class=\"df-node-container\" data-type=\"trigger\"><div class=\"df-node-header\" style=\"background: #10b98111;\"><div class=\"df-node-icon\" style=\"background:#10b981\"><i class=\"bi bi-play-circle\"></i></div><div><div class=\"df-node-title\">Trigger</div><div class=\"df-node-subtitle\">Manual</div></div></div></div>",
          "name": "trigger",
          "class": "trigger",
          "pos_x": 68,
          "pos_y": 41,
          "inputs": {},
          "outputs": {
            "output_1": {
              "connections": [
                {
                  "node": "2",
                  "output": "input_1"
                }
              ]
            }
          },
          "typenode": false
        },
        "2": {
          "id": 2,
          "data": {
            "type": "rotina",
            "label": "Rotina",
            "rotina_id": "5",
            "stop_on_error": "false",
            "output_variable": "resultado"
          },
          "html": "<div class=\"df-node-container\" data-type=\"rotina\"><div class=\"df-node-header\" style=\"background: #3b82f611;\"><div class=\"df-node-icon\" style=\"background:#3b82f6\"><i class=\"bi bi-gear-wide-connected\"></i></div><div><div class=\"df-node-title\">Rotina</div><div class=\"df-node-subtitle\">Selecionar rotina</div></div></div><div class=\"df-node-body\"><span class=\"text-muted fst-italic\">Selecionar rotina</span></div></div>",
          "name": "rotina",
          "class": "rotina",
          "pos_x": 218,
          "pos_y": 202,
          "inputs": {
            "input_1": {
              "connections": [
                {
                  "node": "1",
                  "input": "output_1"
                }
              ]
            }
          },
          "outputs": {
            "output_1": {
              "connections": [
                {
                  "node": "8",
                  "output": "input_1"
                }
              ]
            }
          },
          "typenode": false
        },
        "5": {
          "id": 5,
          "data": {
            "type": "email",
            "label": "Email",
            "email_to": "renan.batista@sad.pe.gov.br",
            "email_subject": "CARGA | MENSAL: 4 - Sucesso"
          },
          "html": "<div class=\"df-node-container\" data-type=\"email\"><div class=\"df-node-header\" style=\"background: #ec489911;\"><div class=\"df-node-icon\" style=\"background:#ec4899\"><i class=\"bi bi-envelope\"></i></div><div><div class=\"df-node-title\">Email</div><div class=\"df-node-subtitle\">Configurar</div></div></div></div>",
          "name": "email",
          "class": "email",
          "pos_x": 723,
          "pos_y": 323,
          "inputs": {
            "input_1": {
              "connections": [
                {
                  "node": "8",
                  "input": "output_1"
                }
              ]
            }
          },
          "outputs": {
            "output_1": {
              "connections": [
                {
                  "node": "9",
                  "output": "input_1"
                }
              ]
            }
          },
          "typenode": false
        },
        "6": {
          "id": 6,
          "data": {
            "type": "email",
            "label": "Email",
            "email_to": "renan.batista@sad.pe.gov.br",
            "email_subject": "CARGA | MENSAL: 4 - Falha"
          },
          "html": "<div class=\"df-node-container\" data-type=\"email\"><div class=\"df-node-header\" style=\"background: #ec489911;\"><div class=\"df-node-icon\" style=\"background:#ec4899\"><i class=\"bi bi-envelope\"></i></div><div><div class=\"df-node-title\">Email</div><div class=\"df-node-subtitle\">Configurar</div></div></div></div>",
          "name": "email",
          "class": "email",
          "pos_x": 720,
          "pos_y": 512,
          "inputs": {
            "input_1": {
              "connections": [
                {
                  "node": "8",
                  "input": "output_2"
                }
              ]
            }
          },
          "outputs": {
            "output_1": {
              "connections": [
                {
                  "node": "9",
                  "output": "input_1"
                }
              ]
            }
          },
          "typenode": false
        },
        "8": {
          "id": 8,
          "data": {
            "type": "condition",
            "label": "Condition",
            "operator": "==",
            "left_operand": "{{resultado.sucesso}}",
            "right_operand": "true"
          },
          "html": "<div class=\"df-node-container\" data-type=\"condition\"><div class=\"df-node-header\" style=\"background: #eab30811;\"><div class=\"df-node-icon\" style=\"background:#eab308\"><i class=\"bi bi-signpost-split\"></i></div><div><div class=\"df-node-title\">Condition</div><div class=\"df-node-subtitle\">Configurar condição</div></div></div><div class=\"df-node-body\"><span class=\"text-muted fst-italic\">Configurar condição</span></div></div>",
          "name": "condition",
          "class": "condition",
          "pos_x": 418,
          "pos_y": 396,
          "inputs": {
            "input_1": {
              "connections": [
                {
                  "node": "2",
                  "input": "output_1"
                }
              ]
            }
          },
          "outputs": {
            "output_1": {
              "connections": [
                {
                  "node": "5",
                  "output": "input_1"
                }
              ]
            },
            "output_2": {
              "connections": [
                {
                  "node": "6",
                  "output": "input_1"
                }
              ]
            }
          },
          "typenode": false
        },
        "9": {
          "id": 9,
          "data": {
            "type": "end",
            "label": "End"
          },
          "html": "<div class=\"df-node-container\" data-type=\"end\"><div class=\"df-node-header\" style=\"background: #dc262611;\"><div class=\"df-node-icon\" style=\"background:#dc2626\"><i class=\"bi bi-stop-circle\"></i></div><div><div class=\"df-node-title\">End</div></div></div></div>",
          "name": "end",
          "class": "end",
          "pos_x": 999,
          "pos_y": 414,
          "inputs": {
            "input_1": {
              "connections": [
                {
                  "node": "5",
                  "input": "output_1"
                },
                {
                  "node": "6",
                  "input": "output_1"
                }
              ]
            }
          },
          "outputs": {},
          "typenode": false
        }
      }
    }
  }
}', '{}', '', 'manual', '{"trigger_type": "manual"}', 2, '[]', 1, '2026-03-26 11:11:16.724199-03', '2026-03-26 11:12:18.307992-03');


--
-- TOC entry 5566 (class 0 OID 46705)
-- Dependencies: 296
-- Data for Name: tb_politica_retencao_arquivos; Type: TABLE DATA; Schema: public; Owner: postgres
--



--
-- TOC entry 5541 (class 0 OID 46365)
-- Dependencies: 271
-- Data for Name: tb_projetos; Type: TABLE DATA; Schema: public; Owner: postgres
--

INSERT INTO public.tb_projetos VALUES (3, 'SAD-PE | SGD - HOMOLOGAÇÃO', '', 1, true, 1, '2026-03-25 15:32:52.553874-03', '2026-03-27 10:55:21.674216-03');
INSERT INTO public.tb_projetos VALUES (4, 'SAD-PE | SGD - PRODUÇÃO', '', 1, true, 1, '2026-03-25 15:33:02.455666-03', '2026-03-27 10:55:33.860646-03');
INSERT INTO public.tb_projetos VALUES (1, 'SAD-PE | SGIC - HOMOLOGAÇÃO', '', 1, true, 1, '2026-03-25 15:22:45.405999-03', '2026-03-27 10:55:43.645484-03');
INSERT INTO public.tb_projetos VALUES (2, 'SAD-PE | SGIC - PRODUÇÃO', '', 1, true, 1, '2026-03-25 15:22:53.355742-03', '2026-03-27 10:55:50.40193-03');
INSERT INTO public.tb_projetos VALUES (123, 'SAD-PE | SGP - PRODUÇÃO', '', 1, true, 1, '2026-03-20 14:30:55.55537-03', '2026-03-27 10:56:16.774854-03');
INSERT INTO public.tb_projetos VALUES (122, 'SAD-PE | HOMOLOGAÇÃO', '', 1, true, 1, '2026-03-20 14:30:44.907474-03', '2026-03-27 10:56:28.088041-03');
INSERT INTO public.tb_projetos VALUES (124, 'SAD-PE | TREINAMENTO', '', 1, true, 1, '2026-03-20 14:31:04.697582-03', '2026-03-27 10:56:36.245346-03');
INSERT INTO public.tb_projetos VALUES (5, 'SAD-PE | SGP - HOMOLOGAÇÃO', '', 1, true, 1, '2026-03-27 10:57:27.565051-03', '2026-03-27 10:57:27.565051-03');
INSERT INTO public.tb_projetos VALUES (6, 'SAD-PE | SGP - TREINAMENTO', '', 1, true, 1, '2026-03-27 10:57:36.466397-03', '2026-03-27 10:57:36.466397-03');
INSERT INTO public.tb_projetos VALUES (7, 'SAD-PE | SUTIN - LUCAS', '', 1, true, 1, '2026-03-27 11:00:31.403395-03', '2026-03-27 11:00:31.403395-03');


--
-- TOC entry 5537 (class 0 OID 46324)
-- Dependencies: 267
-- Data for Name: tb_rate_limits; Type: TABLE DATA; Schema: public; Owner: postgres
--



--
-- TOC entry 5547 (class 0 OID 46435)
-- Dependencies: 277
-- Data for Name: tb_recurso_empresas; Type: TABLE DATA; Schema: public; Owner: postgres
--

INSERT INTO public.tb_recurso_empresas VALUES (26, 'rotina', 3, 1, '2026-03-25 15:04:30.449593-03');
INSERT INTO public.tb_recurso_empresas VALUES (34, 'conexao', 2, 1, '2026-03-25 15:23:19.816157-03');
INSERT INTO public.tb_recurso_empresas VALUES (35, 'conexao', 4, 1, '2026-03-25 15:23:37.934091-03');
INSERT INTO public.tb_recurso_empresas VALUES (36, 'rotina', 1, 1, '2026-03-25 15:23:59.688635-03');
INSERT INTO public.tb_recurso_empresas VALUES (37, 'rotina', 4, 1, '2026-03-25 15:24:11.419012-03');
INSERT INTO public.tb_recurso_empresas VALUES (38, 'rotina', 2, 1, '2026-03-25 15:24:24.61387-03');
INSERT INTO public.tb_recurso_empresas VALUES (39, 'rotina', 5, 1, '2026-03-25 15:24:39.204227-03');
INSERT INTO public.tb_recurso_empresas VALUES (40, 'pipeline', 34, 1, '2026-03-25 15:26:05.103514-03');
INSERT INTO public.tb_recurso_empresas VALUES (41, 'pipeline', 1, 1, '2026-03-25 15:26:18.299991-03');
INSERT INTO public.tb_recurso_empresas VALUES (43, 'conexao', 5, 1, '2026-03-25 15:33:31.403222-03');
INSERT INTO public.tb_recurso_empresas VALUES (44, 'conexao', 6, 1, '2026-03-25 15:34:44.052817-03');
INSERT INTO public.tb_recurso_empresas VALUES (47, 'pipeline', 35, 1, '2026-03-26 11:12:18.311167-03');
INSERT INTO public.tb_recurso_empresas VALUES (48, 'pipeline', 36, 1, '2026-03-26 11:13:10.346176-03');
INSERT INTO public.tb_recurso_empresas VALUES (50, 'rotina', 6, 1, '2026-03-26 16:48:49.887032-03');
INSERT INTO public.tb_recurso_empresas VALUES (51, 'conexao', 8, 1, '2026-03-27 10:57:51.997103-03');
INSERT INTO public.tb_recurso_empresas VALUES (52, 'conexao', 7, 1, '2026-03-27 10:58:07.151088-03');
INSERT INTO public.tb_recurso_empresas VALUES (53, 'conexao', 1, 1, '2026-03-27 11:01:27.727053-03');


--
-- TOC entry 5549 (class 0 OID 46452)
-- Dependencies: 279
-- Data for Name: tb_recurso_projetos; Type: TABLE DATA; Schema: public; Owner: postgres
--

INSERT INTO public.tb_recurso_projetos VALUES (31, 'rotina', 3, 122, '2026-03-25 15:04:30.449593-03');
INSERT INTO public.tb_recurso_projetos VALUES (39, 'conexao', 2, 1, '2026-03-25 15:23:19.820014-03');
INSERT INTO public.tb_recurso_projetos VALUES (40, 'conexao', 4, 2, '2026-03-25 15:23:37.936837-03');
INSERT INTO public.tb_recurso_projetos VALUES (41, 'rotina', 1, 1, '2026-03-25 15:23:59.691604-03');
INSERT INTO public.tb_recurso_projetos VALUES (42, 'rotina', 4, 2, '2026-03-25 15:24:11.423701-03');
INSERT INTO public.tb_recurso_projetos VALUES (43, 'rotina', 2, 1, '2026-03-25 15:24:24.618174-03');
INSERT INTO public.tb_recurso_projetos VALUES (44, 'rotina', 5, 2, '2026-03-25 15:24:39.207602-03');
INSERT INTO public.tb_recurso_projetos VALUES (45, 'pipeline', 34, 1, '2026-03-25 15:26:05.107146-03');
INSERT INTO public.tb_recurso_projetos VALUES (46, 'pipeline', 1, 1, '2026-03-25 15:26:18.304012-03');
INSERT INTO public.tb_recurso_projetos VALUES (47, 'conexao', 5, 3, '2026-03-25 15:33:31.40618-03');
INSERT INTO public.tb_recurso_projetos VALUES (48, 'conexao', 6, 4, '2026-03-25 15:34:44.056727-03');
INSERT INTO public.tb_recurso_projetos VALUES (51, 'pipeline', 35, 2, '2026-03-26 11:12:18.313766-03');
INSERT INTO public.tb_recurso_projetos VALUES (52, 'pipeline', 36, 2, '2026-03-26 11:13:10.348357-03');
INSERT INTO public.tb_recurso_projetos VALUES (54, 'rotina', 6, 3, '2026-03-26 16:48:49.890514-03');
INSERT INTO public.tb_recurso_projetos VALUES (55, 'conexao', 8, 5, '2026-03-27 10:57:51.999701-03');
INSERT INTO public.tb_recurso_projetos VALUES (56, 'conexao', 7, 6, '2026-03-27 10:58:07.155461-03');
INSERT INTO public.tb_recurso_projetos VALUES (57, 'conexao', 1, 7, '2026-03-27 11:01:27.730171-03');


--
-- TOC entry 5501 (class 0 OID 45572)
-- Dependencies: 230
-- Data for Name: tb_rotinas; Type: TABLE DATA; Schema: public; Owner: postgres
--

INSERT INTO public.tb_rotinas OVERRIDING SYSTEM VALUE VALUES (2, 'CARGA | MENSAL: 4 | HOMOLOGAÇÃO', 'Atualização da base SGIC (OUTRAS ENTIDADES)', 2, 1, false, NULL, NULL, NULL, '2026-03-25 14:37:31.212867-03', NULL, false, NULL, 0, 3, NULL, NULL, NULL, NULL, false, 300, true, true, false);
INSERT INTO public.tb_rotinas OVERRIDING SYSTEM VALUE VALUES (5, 'CARGA | MENSAL: 4 | PRODUÇÃO', 'Atualização da base SGIC (OUTRAS ENTIDADES)', 4, 1, false, NULL, NULL, NULL, '2026-03-25 15:06:44.390066-03', NULL, false, NULL, 0, 3, NULL, NULL, NULL, NULL, false, 300, true, true, false);
INSERT INTO public.tb_rotinas OVERRIDING SYSTEM VALUE VALUES (1, 'CARGA | MENSAL: 3 | HOMOLOGAÇÃO', 'Atualização de bases de homologação SGD para SGIC', 2, 1, false, '2026-03-26 12:10:24.017997-03', NULL, NULL, '2026-03-25 10:03:34.637042-03', NULL, false, NULL, 0, 3, '2026-03-26 12:10:24.017997-03', NULL, NULL, NULL, false, 300, true, true, false);
INSERT INTO public.tb_rotinas OVERRIDING SYSTEM VALUE VALUES (4, 'CARGA | MENSAL: 3 | PRODUÇÃO', 'Atualização de bases de homologação SGD para SGIC', 4, 1, false, '2026-03-26 13:17:03.75648-03', NULL, NULL, '2026-03-25 15:06:23.952566-03', NULL, false, NULL, 0, 3, '2026-03-26 13:17:03.75648-03', NULL, NULL, NULL, false, 300, true, true, false);
INSERT INTO public.tb_rotinas OVERRIDING SYSTEM VALUE VALUES (6, 'CARGA | MENSAL: 2 | HOMOLOGAÇÃO', 'Atualização de bases de homologação SGP para SGD', 5, 1, false, NULL, NULL, NULL, '2026-03-26 16:45:33.75167-03', NULL, false, NULL, 0, 3, NULL, NULL, NULL, NULL, false, 300, true, true, false);


--
-- TOC entry 5543 (class 0 OID 46391)
-- Dependencies: 273
-- Data for Name: tb_usuario_empresas; Type: TABLE DATA; Schema: public; Owner: postgres
--

INSERT INTO public.tb_usuario_empresas VALUES (17, 111, 1, '2026-03-27 10:59:45.215151-03');
INSERT INTO public.tb_usuario_empresas VALUES (18, 3, 1, '2026-03-27 10:59:59.752768-03');
INSERT INTO public.tb_usuario_empresas VALUES (19, 112, 1, '2026-03-27 11:00:06.597703-03');
INSERT INTO public.tb_usuario_empresas VALUES (20, 32, 1, '2026-03-27 11:00:42.192268-03');


--
-- TOC entry 5545 (class 0 OID 46413)
-- Dependencies: 275
-- Data for Name: tb_usuario_projetos; Type: TABLE DATA; Schema: public; Owner: postgres
--

INSERT INTO public.tb_usuario_projetos VALUES (53, 111, 122, '2026-03-27 10:59:45.215151-03');
INSERT INTO public.tb_usuario_projetos VALUES (54, 111, 3, '2026-03-27 10:59:45.215151-03');
INSERT INTO public.tb_usuario_projetos VALUES (55, 111, 4, '2026-03-27 10:59:45.215151-03');
INSERT INTO public.tb_usuario_projetos VALUES (56, 111, 1, '2026-03-27 10:59:45.215151-03');
INSERT INTO public.tb_usuario_projetos VALUES (57, 111, 2, '2026-03-27 10:59:45.215151-03');
INSERT INTO public.tb_usuario_projetos VALUES (58, 111, 5, '2026-03-27 10:59:45.215151-03');
INSERT INTO public.tb_usuario_projetos VALUES (59, 111, 123, '2026-03-27 10:59:45.215151-03');
INSERT INTO public.tb_usuario_projetos VALUES (60, 111, 6, '2026-03-27 10:59:45.215151-03');
INSERT INTO public.tb_usuario_projetos VALUES (61, 111, 124, '2026-03-27 10:59:45.215151-03');
INSERT INTO public.tb_usuario_projetos VALUES (62, 3, 122, '2026-03-27 10:59:59.752768-03');
INSERT INTO public.tb_usuario_projetos VALUES (63, 3, 3, '2026-03-27 10:59:59.752768-03');
INSERT INTO public.tb_usuario_projetos VALUES (64, 3, 1, '2026-03-27 10:59:59.752768-03');
INSERT INTO public.tb_usuario_projetos VALUES (65, 3, 5, '2026-03-27 10:59:59.752768-03');
INSERT INTO public.tb_usuario_projetos VALUES (66, 3, 6, '2026-03-27 10:59:59.752768-03');
INSERT INTO public.tb_usuario_projetos VALUES (67, 112, 122, '2026-03-27 11:00:06.597703-03');
INSERT INTO public.tb_usuario_projetos VALUES (68, 112, 1, '2026-03-27 11:00:06.597703-03');
INSERT INTO public.tb_usuario_projetos VALUES (69, 112, 5, '2026-03-27 11:00:06.597703-03');
INSERT INTO public.tb_usuario_projetos VALUES (70, 112, 6, '2026-03-27 11:00:06.597703-03');
INSERT INTO public.tb_usuario_projetos VALUES (71, 32, 122, '2026-03-27 11:00:42.192268-03');
INSERT INTO public.tb_usuario_projetos VALUES (72, 32, 5, '2026-03-27 11:00:42.192268-03');
INSERT INTO public.tb_usuario_projetos VALUES (73, 32, 123, '2026-03-27 11:00:42.192268-03');
INSERT INTO public.tb_usuario_projetos VALUES (74, 32, 6, '2026-03-27 11:00:42.192268-03');
INSERT INTO public.tb_usuario_projetos VALUES (75, 32, 7, '2026-03-27 11:00:42.192268-03');


--
-- TOC entry 5497 (class 0 OID 45550)
-- Dependencies: 226
-- Data for Name: tb_usuarios; Type: TABLE DATA; Schema: public; Owner: postgres
--

INSERT INTO public.tb_usuarios OVERRIDING SYSTEM VALUE VALUES (1, 'admin', '$2y$10$geM75sPZ4ivOpkAbKflNe.QWZyQq1xwWRAmnievjl/AtJxc9kD4/6', false, 'super_admin', '2026-02-02 11:08:48.320843-03', false, NULL, NULL, NULL, NULL);
INSERT INTO public.tb_usuarios OVERRIDING SYSTEM VALUE VALUES (111, 'caio', '$2y$10$kZiLCSFqL7aChIonZMQgsOmqzDbYYYLV/oo36teOkWnFDkcGXpqA2', false, 'desenvolvedor', '2026-03-25 15:25:19.641798-03', false, 'Caio Vinícius', 'caio.barros@sad.pe.gov.br', '121.018.404-48', NULL);
INSERT INTO public.tb_usuarios OVERRIDING SYSTEM VALUE VALUES (3, 'leonardo', '$2y$10$56fEHuaY.2mxdTMFDieZbeRByXFPCshQ0m/23EA/xBhKNb2BHZYWO', false, 'desenvolvedor', '2026-02-03 09:27:27.440711-03', false, 'Leonardo Matias', 'leonardo.matias@sad.pe.gov.br', NULL, NULL);
INSERT INTO public.tb_usuarios OVERRIDING SYSTEM VALUE VALUES (32, 'lucas', '$2y$10$iQ0L5d7xtSLMEYpVvo.9p.lN7dHqXgtDbXrcKpdUgKP/NWdfpBOpW', false, 'desenvolvedor', '2026-03-20 10:28:09.786803-03', false, 'Sobral', 'lucas.sobral@sad.pe.gov.br', NULL, NULL);
INSERT INTO public.tb_usuarios OVERRIDING SYSTEM VALUE VALUES (112, 'renan', '$2y$10$iPn7L9Osswt5pBsnF3Rk/OLit1E5XlJl4.Z0CbH2ObNz/ptAw2I/a', false, 'desenvolvedor', '2026-03-26 11:10:20.83579-03', false, 'Renan prostituto da silva', 'renan.batista@sad.pe.gov.br', NULL, NULL);


--
-- TOC entry 5519 (class 0 OID 45756)
-- Dependencies: 249
-- Data for Name: tb_valores_capturados; Type: TABLE DATA; Schema: public; Owner: postgres
--



--
-- TOC entry 5556 (class 0 OID 46535)
-- Dependencies: 286
-- Data for Name: tb_webhooks; Type: TABLE DATA; Schema: public; Owner: postgres
--



--
-- TOC entry 5511 (class 0 OID 45658)
-- Dependencies: 240
-- Data for Name: tb_worker_heartbeat; Type: TABLE DATA; Schema: public; Owner: postgres
--



--
-- TOC entry 5525 (class 0 OID 45822)
-- Dependencies: 255
-- Data for Name: tb_workflow_edges; Type: TABLE DATA; Schema: public; Owner: postgres
--



--
-- TOC entry 5527 (class 0 OID 45844)
-- Dependencies: 257
-- Data for Name: tb_workflow_execucoes; Type: TABLE DATA; Schema: public; Owner: postgres
--



--
-- TOC entry 5529 (class 0 OID 45872)
-- Dependencies: 259
-- Data for Name: tb_workflow_node_execucoes; Type: TABLE DATA; Schema: public; Owner: postgres
--



--
-- TOC entry 5523 (class 0 OID 45799)
-- Dependencies: 253
-- Data for Name: tb_workflow_nodes; Type: TABLE DATA; Schema: public; Owner: postgres
--



--
-- TOC entry 5521 (class 0 OID 45781)
-- Dependencies: 251
-- Data for Name: tb_workflows; Type: TABLE DATA; Schema: public; Owner: postgres
--



--
-- TOC entry 5620 (class 0 OID 0)
-- Dependencies: 221
-- Name: connections_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.connections_id_seq', 1, false);


--
-- TOC entry 5621 (class 0 OID 0)
-- Dependencies: 242
-- Name: logs_sistema_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.logs_sistema_id_seq', 1, false);


--
-- TOC entry 5622 (class 0 OID 0)
-- Dependencies: 223
-- Name: schedules_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.schedules_id_seq', 1, false);


--
-- TOC entry 5623 (class 0 OID 0)
-- Dependencies: 244
-- Name: tb_api_externas_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.tb_api_externas_id_seq', 1, true);


--
-- TOC entry 5624 (class 0 OID 0)
-- Dependencies: 217
-- Name: tb_arquivos_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.tb_arquivos_id_seq', 1, false);


--
-- TOC entry 5625 (class 0 OID 0)
-- Dependencies: 282
-- Name: tb_auditoria_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.tb_auditoria_id_seq', 131, true);


--
-- TOC entry 5626 (class 0 OID 0)
-- Dependencies: 219
-- Name: tb_auditoria_rotina_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.tb_auditoria_rotina_id_seq', 1, false);


--
-- TOC entry 5627 (class 0 OID 0)
-- Dependencies: 291
-- Name: tb_backups_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.tb_backups_id_seq', 1, false);


--
-- TOC entry 5628 (class 0 OID 0)
-- Dependencies: 231
-- Name: tb_blocos_rotina_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.tb_blocos_rotina_id_seq', 709, true);


--
-- TOC entry 5629 (class 0 OID 0)
-- Dependencies: 289
-- Name: tb_canais_notificacao_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.tb_canais_notificacao_id_seq', 1, false);


--
-- TOC entry 5630 (class 0 OID 0)
-- Dependencies: 280
-- Name: tb_compartilhamentos_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.tb_compartilhamentos_id_seq', 1, false);


--
-- TOC entry 5631 (class 0 OID 0)
-- Dependencies: 268
-- Name: tb_empresas_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.tb_empresas_id_seq', 1, false);


--
-- TOC entry 5632 (class 0 OID 0)
-- Dependencies: 246
-- Name: tb_eventos_api_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.tb_eventos_api_id_seq', 1, false);


--
-- TOC entry 5633 (class 0 OID 0)
-- Dependencies: 287
-- Name: tb_fila_execucao_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.tb_fila_execucao_id_seq', 1, false);


--
-- TOC entry 5634 (class 0 OID 0)
-- Dependencies: 233
-- Name: tb_logs_execucao_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.tb_logs_execucao_id_seq', 22, true);


--
-- TOC entry 5635 (class 0 OID 0)
-- Dependencies: 235
-- Name: tb_logs_sistema_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.tb_logs_sistema_id_seq', 1, true);


--
-- TOC entry 5636 (class 0 OID 0)
-- Dependencies: 237
-- Name: tb_metricas_sistema_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.tb_metricas_sistema_id_seq', 1, false);


--
-- TOC entry 5637 (class 0 OID 0)
-- Dependencies: 264
-- Name: tb_notificacoes_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.tb_notificacoes_id_seq', 19, true);


--
-- TOC entry 5638 (class 0 OID 0)
-- Dependencies: 293
-- Name: tb_password_resets_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.tb_password_resets_id_seq', 1, true);


--
-- TOC entry 5639 (class 0 OID 0)
-- Dependencies: 227
-- Name: tb_perfis_conexao_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.tb_perfis_conexao_id_seq', 8, true);


--
-- TOC entry 5640 (class 0 OID 0)
-- Dependencies: 262
-- Name: tb_pipeline_execucoes_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.tb_pipeline_execucoes_id_seq', 4, true);


--
-- TOC entry 5641 (class 0 OID 0)
-- Dependencies: 260
-- Name: tb_pipelines_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.tb_pipelines_id_seq', 36, true);


--
-- TOC entry 5642 (class 0 OID 0)
-- Dependencies: 295
-- Name: tb_politica_retencao_arquivos_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.tb_politica_retencao_arquivos_id_seq', 1, false);


--
-- TOC entry 5643 (class 0 OID 0)
-- Dependencies: 270
-- Name: tb_projetos_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.tb_projetos_id_seq', 7, true);


--
-- TOC entry 5644 (class 0 OID 0)
-- Dependencies: 266
-- Name: tb_rate_limits_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.tb_rate_limits_id_seq', 79, true);


--
-- TOC entry 5645 (class 0 OID 0)
-- Dependencies: 276
-- Name: tb_recurso_empresas_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.tb_recurso_empresas_id_seq', 53, true);


--
-- TOC entry 5646 (class 0 OID 0)
-- Dependencies: 278
-- Name: tb_recurso_projetos_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.tb_recurso_projetos_id_seq', 57, true);


--
-- TOC entry 5647 (class 0 OID 0)
-- Dependencies: 229
-- Name: tb_rotinas_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.tb_rotinas_id_seq', 6, true);


--
-- TOC entry 5648 (class 0 OID 0)
-- Dependencies: 272
-- Name: tb_usuario_empresas_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.tb_usuario_empresas_id_seq', 20, true);


--
-- TOC entry 5649 (class 0 OID 0)
-- Dependencies: 274
-- Name: tb_usuario_projetos_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.tb_usuario_projetos_id_seq', 75, true);


--
-- TOC entry 5650 (class 0 OID 0)
-- Dependencies: 225
-- Name: tb_usuarios_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.tb_usuarios_id_seq', 112, true);


--
-- TOC entry 5651 (class 0 OID 0)
-- Dependencies: 248
-- Name: tb_valores_capturados_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.tb_valores_capturados_id_seq', 1, false);


--
-- TOC entry 5652 (class 0 OID 0)
-- Dependencies: 285
-- Name: tb_webhooks_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.tb_webhooks_id_seq', 1, true);


--
-- TOC entry 5653 (class 0 OID 0)
-- Dependencies: 239
-- Name: tb_worker_heartbeat_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.tb_worker_heartbeat_id_seq', 1, false);


--
-- TOC entry 5654 (class 0 OID 0)
-- Dependencies: 254
-- Name: tb_workflow_edges_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.tb_workflow_edges_id_seq', 1, false);


--
-- TOC entry 5655 (class 0 OID 0)
-- Dependencies: 256
-- Name: tb_workflow_execucoes_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.tb_workflow_execucoes_id_seq', 1, false);


--
-- TOC entry 5656 (class 0 OID 0)
-- Dependencies: 258
-- Name: tb_workflow_node_execucoes_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.tb_workflow_node_execucoes_id_seq', 1, false);


--
-- TOC entry 5657 (class 0 OID 0)
-- Dependencies: 252
-- Name: tb_workflow_nodes_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.tb_workflow_nodes_id_seq', 1, false);


--
-- TOC entry 5658 (class 0 OID 0)
-- Dependencies: 250
-- Name: tb_workflows_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.tb_workflows_id_seq', 1, false);


--
-- TOC entry 5136 (class 2606 OID 45527)
-- Name: connections connections_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.connections
    ADD CONSTRAINT connections_pkey PRIMARY KEY (id);


--
-- TOC entry 5173 (class 2606 OID 45686)
-- Name: logs_sistema logs_sistema_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.logs_sistema
    ADD CONSTRAINT logs_sistema_pkey PRIMARY KEY (id);


--
-- TOC entry 5138 (class 2606 OID 45536)
-- Name: schedules schedules_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.schedules
    ADD CONSTRAINT schedules_pkey PRIMARY KEY (id);


--
-- TOC entry 5177 (class 2606 OID 45728)
-- Name: tb_api_externas tb_api_externas_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tb_api_externas
    ADD CONSTRAINT tb_api_externas_pkey PRIMARY KEY (id);


--
-- TOC entry 5132 (class 2606 OID 45493)
-- Name: tb_arquivos tb_arquivos_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tb_arquivos
    ADD CONSTRAINT tb_arquivos_pkey PRIMARY KEY (id);


--
-- TOC entry 5283 (class 2606 OID 46518)
-- Name: tb_auditoria tb_auditoria_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tb_auditoria
    ADD CONSTRAINT tb_auditoria_pkey PRIMARY KEY (id);


--
-- TOC entry 5134 (class 2606 OID 45502)
-- Name: tb_auditoria_rotina tb_auditoria_rotina_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tb_auditoria_rotina
    ADD CONSTRAINT tb_auditoria_rotina_pkey PRIMARY KEY (id);


--
-- TOC entry 5303 (class 2606 OID 46598)
-- Name: tb_backups tb_backups_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tb_backups
    ADD CONSTRAINT tb_backups_pkey PRIMARY KEY (id);


--
-- TOC entry 5151 (class 2606 OID 45600)
-- Name: tb_blocos_rotina tb_blocos_rotina_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tb_blocos_rotina
    ADD CONSTRAINT tb_blocos_rotina_pkey PRIMARY KEY (id);


--
-- TOC entry 5299 (class 2606 OID 46584)
-- Name: tb_canais_notificacao tb_canais_notificacao_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tb_canais_notificacao
    ADD CONSTRAINT tb_canais_notificacao_pkey PRIMARY KEY (id);


--
-- TOC entry 5274 (class 2606 OID 46476)
-- Name: tb_compartilhamentos tb_compartilhamentos_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tb_compartilhamentos
    ADD CONSTRAINT tb_compartilhamentos_pkey PRIMARY KEY (id);


--
-- TOC entry 5276 (class 2606 OID 46478)
-- Name: tb_compartilhamentos tb_compartilhamentos_tipo_recurso_id_recurso_id_usuario_don_key; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tb_compartilhamentos
    ADD CONSTRAINT tb_compartilhamentos_tipo_recurso_id_recurso_id_usuario_don_key UNIQUE (tipo_recurso, id_recurso, id_usuario_dono, id_usuario_destino);


--
-- TOC entry 5286 (class 2606 OID 46532)
-- Name: tb_configuracoes tb_configuracoes_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tb_configuracoes
    ADD CONSTRAINT tb_configuracoes_pkey PRIMARY KEY (chave);


--
-- TOC entry 5237 (class 2606 OID 46357)
-- Name: tb_empresas tb_empresas_nome_key; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tb_empresas
    ADD CONSTRAINT tb_empresas_nome_key UNIQUE (nome);


--
-- TOC entry 5239 (class 2606 OID 46355)
-- Name: tb_empresas tb_empresas_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tb_empresas
    ADD CONSTRAINT tb_empresas_pkey PRIMARY KEY (id);


--
-- TOC entry 5182 (class 2606 OID 45746)
-- Name: tb_eventos_api tb_eventos_api_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tb_eventos_api
    ADD CONSTRAINT tb_eventos_api_pkey PRIMARY KEY (id);


--
-- TOC entry 5295 (class 2606 OID 46563)
-- Name: tb_fila_execucao tb_fila_execucao_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tb_fila_execucao
    ADD CONSTRAINT tb_fila_execucao_pkey PRIMARY KEY (id);


--
-- TOC entry 5156 (class 2606 OID 45614)
-- Name: tb_logs_execucao tb_logs_execucao_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tb_logs_execucao
    ADD CONSTRAINT tb_logs_execucao_pkey PRIMARY KEY (id);


--
-- TOC entry 5161 (class 2606 OID 45642)
-- Name: tb_logs_sistema tb_logs_sistema_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tb_logs_sistema
    ADD CONSTRAINT tb_logs_sistema_pkey PRIMARY KEY (id);


--
-- TOC entry 5163 (class 2606 OID 45656)
-- Name: tb_metricas_sistema tb_metricas_sistema_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tb_metricas_sistema
    ADD CONSTRAINT tb_metricas_sistema_pkey PRIMARY KEY (id);


--
-- TOC entry 5231 (class 2606 OID 46319)
-- Name: tb_notificacoes tb_notificacoes_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tb_notificacoes
    ADD CONSTRAINT tb_notificacoes_pkey PRIMARY KEY (id);


--
-- TOC entry 5307 (class 2606 OID 46610)
-- Name: tb_password_resets tb_password_resets_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tb_password_resets
    ADD CONSTRAINT tb_password_resets_pkey PRIMARY KEY (id);


--
-- TOC entry 5142 (class 2606 OID 45568)
-- Name: tb_perfis_conexao tb_perfis_conexao_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tb_perfis_conexao
    ADD CONSTRAINT tb_perfis_conexao_pkey PRIMARY KEY (id);


--
-- TOC entry 5226 (class 2606 OID 46293)
-- Name: tb_pipeline_execucoes tb_pipeline_execucoes_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tb_pipeline_execucoes
    ADD CONSTRAINT tb_pipeline_execucoes_pkey PRIMARY KEY (id);


--
-- TOC entry 5219 (class 2606 OID 46276)
-- Name: tb_pipelines tb_pipelines_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tb_pipelines
    ADD CONSTRAINT tb_pipelines_pkey PRIMARY KEY (id);


--
-- TOC entry 5309 (class 2606 OID 46714)
-- Name: tb_politica_retencao_arquivos tb_politica_retencao_arquivos_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tb_politica_retencao_arquivos
    ADD CONSTRAINT tb_politica_retencao_arquivos_pkey PRIMARY KEY (id);


--
-- TOC entry 5243 (class 2606 OID 46377)
-- Name: tb_projetos tb_projetos_nome_id_empresa_key; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tb_projetos
    ADD CONSTRAINT tb_projetos_nome_id_empresa_key UNIQUE (nome, id_empresa);


--
-- TOC entry 5245 (class 2606 OID 46375)
-- Name: tb_projetos tb_projetos_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tb_projetos
    ADD CONSTRAINT tb_projetos_pkey PRIMARY KEY (id);


--
-- TOC entry 5234 (class 2606 OID 46332)
-- Name: tb_rate_limits tb_rate_limits_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tb_rate_limits
    ADD CONSTRAINT tb_rate_limits_pkey PRIMARY KEY (id);


--
-- TOC entry 5261 (class 2606 OID 46441)
-- Name: tb_recurso_empresas tb_recurso_empresas_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tb_recurso_empresas
    ADD CONSTRAINT tb_recurso_empresas_pkey PRIMARY KEY (id);


--
-- TOC entry 5263 (class 2606 OID 46443)
-- Name: tb_recurso_empresas tb_recurso_empresas_tipo_recurso_id_recurso_id_empresa_key; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tb_recurso_empresas
    ADD CONSTRAINT tb_recurso_empresas_tipo_recurso_id_recurso_id_empresa_key UNIQUE (tipo_recurso, id_recurso, id_empresa);


--
-- TOC entry 5267 (class 2606 OID 46458)
-- Name: tb_recurso_projetos tb_recurso_projetos_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tb_recurso_projetos
    ADD CONSTRAINT tb_recurso_projetos_pkey PRIMARY KEY (id);


--
-- TOC entry 5269 (class 2606 OID 46460)
-- Name: tb_recurso_projetos tb_recurso_projetos_tipo_recurso_id_recurso_id_projeto_key; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tb_recurso_projetos
    ADD CONSTRAINT tb_recurso_projetos_tipo_recurso_id_recurso_id_projeto_key UNIQUE (tipo_recurso, id_recurso, id_projeto);


--
-- TOC entry 5149 (class 2606 OID 45580)
-- Name: tb_rotinas tb_rotinas_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tb_rotinas
    ADD CONSTRAINT tb_rotinas_pkey PRIMARY KEY (id);


--
-- TOC entry 5249 (class 2606 OID 46399)
-- Name: tb_usuario_empresas tb_usuario_empresas_id_usuario_id_empresa_key; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tb_usuario_empresas
    ADD CONSTRAINT tb_usuario_empresas_id_usuario_id_empresa_key UNIQUE (id_usuario, id_empresa);


--
-- TOC entry 5251 (class 2606 OID 46397)
-- Name: tb_usuario_empresas tb_usuario_empresas_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tb_usuario_empresas
    ADD CONSTRAINT tb_usuario_empresas_pkey PRIMARY KEY (id);


--
-- TOC entry 5255 (class 2606 OID 46421)
-- Name: tb_usuario_projetos tb_usuario_projetos_id_usuario_id_projeto_key; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tb_usuario_projetos
    ADD CONSTRAINT tb_usuario_projetos_id_usuario_id_projeto_key UNIQUE (id_usuario, id_projeto);


--
-- TOC entry 5257 (class 2606 OID 46419)
-- Name: tb_usuario_projetos tb_usuario_projetos_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tb_usuario_projetos
    ADD CONSTRAINT tb_usuario_projetos_pkey PRIMARY KEY (id);


--
-- TOC entry 5140 (class 2606 OID 45559)
-- Name: tb_usuarios tb_usuarios_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tb_usuarios
    ADD CONSTRAINT tb_usuarios_pkey PRIMARY KEY (id);


--
-- TOC entry 5187 (class 2606 OID 45766)
-- Name: tb_valores_capturados tb_valores_capturados_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tb_valores_capturados
    ADD CONSTRAINT tb_valores_capturados_pkey PRIMARY KEY (id);


--
-- TOC entry 5288 (class 2606 OID 46547)
-- Name: tb_webhooks tb_webhooks_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tb_webhooks
    ADD CONSTRAINT tb_webhooks_pkey PRIMARY KEY (id);


--
-- TOC entry 5167 (class 2606 OID 45667)
-- Name: tb_worker_heartbeat tb_worker_heartbeat_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tb_worker_heartbeat
    ADD CONSTRAINT tb_worker_heartbeat_pkey PRIMARY KEY (id);


--
-- TOC entry 5169 (class 2606 OID 45669)
-- Name: tb_worker_heartbeat tb_worker_heartbeat_worker_id_key; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tb_worker_heartbeat
    ADD CONSTRAINT tb_worker_heartbeat_worker_id_key UNIQUE (worker_id);


--
-- TOC entry 5202 (class 2606 OID 45834)
-- Name: tb_workflow_edges tb_workflow_edges_id_workflow_edge_id_key; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tb_workflow_edges
    ADD CONSTRAINT tb_workflow_edges_id_workflow_edge_id_key UNIQUE (id_workflow, edge_id);


--
-- TOC entry 5204 (class 2606 OID 45832)
-- Name: tb_workflow_edges tb_workflow_edges_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tb_workflow_edges
    ADD CONSTRAINT tb_workflow_edges_pkey PRIMARY KEY (id);


--
-- TOC entry 5209 (class 2606 OID 45862)
-- Name: tb_workflow_execucoes tb_workflow_execucoes_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tb_workflow_execucoes
    ADD CONSTRAINT tb_workflow_execucoes_pkey PRIMARY KEY (id);


--
-- TOC entry 5214 (class 2606 OID 45884)
-- Name: tb_workflow_node_execucoes tb_workflow_node_execucoes_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tb_workflow_node_execucoes
    ADD CONSTRAINT tb_workflow_node_execucoes_pkey PRIMARY KEY (id);


--
-- TOC entry 5195 (class 2606 OID 45813)
-- Name: tb_workflow_nodes tb_workflow_nodes_id_workflow_node_id_key; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tb_workflow_nodes
    ADD CONSTRAINT tb_workflow_nodes_id_workflow_node_id_key UNIQUE (id_workflow, node_id);


--
-- TOC entry 5197 (class 2606 OID 45811)
-- Name: tb_workflow_nodes tb_workflow_nodes_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tb_workflow_nodes
    ADD CONSTRAINT tb_workflow_nodes_pkey PRIMARY KEY (id);


--
-- TOC entry 5191 (class 2606 OID 45795)
-- Name: tb_workflows tb_workflows_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tb_workflows
    ADD CONSTRAINT tb_workflows_pkey PRIMARY KEY (id);


--
-- TOC entry 5311 (class 2606 OID 46716)
-- Name: tb_politica_retencao_arquivos uq_politica_rotina; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tb_politica_retencao_arquivos
    ADD CONSTRAINT uq_politica_rotina UNIQUE (id_rotina);


--
-- TOC entry 5144 (class 2606 OID 45570)
-- Name: tb_perfis_conexao uq_tb_perfis_conexao_nome; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tb_perfis_conexao
    ADD CONSTRAINT uq_tb_perfis_conexao_nome UNIQUE (nome_conexao);


--
-- TOC entry 5174 (class 1259 OID 45729)
-- Name: idx_api_externas_ativo; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_api_externas_ativo ON public.tb_api_externas USING btree (ativo);


--
-- TOC entry 5175 (class 1259 OID 45730)
-- Name: idx_api_externas_nome; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_api_externas_nome ON public.tb_api_externas USING btree (nome);


--
-- TOC entry 5277 (class 1259 OID 46519)
-- Name: idx_auditoria_acao; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_auditoria_acao ON public.tb_auditoria USING btree (acao);


--
-- TOC entry 5278 (class 1259 OID 46522)
-- Name: idx_auditoria_criado_em; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_auditoria_criado_em ON public.tb_auditoria USING btree (criado_em);


--
-- TOC entry 5279 (class 1259 OID 46520)
-- Name: idx_auditoria_entidade; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_auditoria_entidade ON public.tb_auditoria USING btree (entidade);


--
-- TOC entry 5280 (class 1259 OID 46523)
-- Name: idx_auditoria_entidade_id; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_auditoria_entidade_id ON public.tb_auditoria USING btree (entidade, entidade_id);


--
-- TOC entry 5281 (class 1259 OID 46521)
-- Name: idx_auditoria_usuario; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_auditoria_usuario ON public.tb_auditoria USING btree (id_usuario);


--
-- TOC entry 5300 (class 1259 OID 46599)
-- Name: idx_backups_status; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_backups_status ON public.tb_backups USING btree (status);


--
-- TOC entry 5301 (class 1259 OID 46600)
-- Name: idx_backups_tipo; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_backups_tipo ON public.tb_backups USING btree (tipo);


--
-- TOC entry 5296 (class 1259 OID 46586)
-- Name: idx_canais_ativo; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_canais_ativo ON public.tb_canais_notificacao USING btree (ativo);


--
-- TOC entry 5297 (class 1259 OID 46585)
-- Name: idx_canais_tipo; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_canais_tipo ON public.tb_canais_notificacao USING btree (tipo);


--
-- TOC entry 5270 (class 1259 OID 46490)
-- Name: idx_comp_destino; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_comp_destino ON public.tb_compartilhamentos USING btree (id_usuario_destino);


--
-- TOC entry 5271 (class 1259 OID 46489)
-- Name: idx_comp_dono; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_comp_dono ON public.tb_compartilhamentos USING btree (id_usuario_dono);


--
-- TOC entry 5272 (class 1259 OID 46491)
-- Name: idx_comp_recurso; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_comp_recurso ON public.tb_compartilhamentos USING btree (tipo_recurso, id_recurso);


--
-- TOC entry 5284 (class 1259 OID 46533)
-- Name: idx_configuracoes_grupo; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_configuracoes_grupo ON public.tb_configuracoes USING btree (grupo);


--
-- TOC entry 5235 (class 1259 OID 46363)
-- Name: idx_empresas_ativa; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_empresas_ativa ON public.tb_empresas USING btree (ativa);


--
-- TOC entry 5178 (class 1259 OID 45753)
-- Name: idx_eventos_api_ativo; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_eventos_api_ativo ON public.tb_eventos_api USING btree (ativo);


--
-- TOC entry 5179 (class 1259 OID 45752)
-- Name: idx_eventos_api_id_api; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_eventos_api_id_api ON public.tb_eventos_api USING btree (id_api);


--
-- TOC entry 5180 (class 1259 OID 45754)
-- Name: idx_eventos_api_workflow; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_eventos_api_workflow ON public.tb_eventos_api USING btree (id_workflow);


--
-- TOC entry 5289 (class 1259 OID 46567)
-- Name: idx_fila_agendado; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_fila_agendado ON public.tb_fila_execucao USING btree (agendado_para) WHERE ((status)::text = 'pendente'::text);


--
-- TOC entry 5290 (class 1259 OID 46565)
-- Name: idx_fila_prioridade; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_fila_prioridade ON public.tb_fila_execucao USING btree (prioridade, criado_em);


--
-- TOC entry 5291 (class 1259 OID 46564)
-- Name: idx_fila_status; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_fila_status ON public.tb_fila_execucao USING btree (status);


--
-- TOC entry 5292 (class 1259 OID 46566)
-- Name: idx_fila_tipo; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_fila_tipo ON public.tb_fila_execucao USING btree (tipo, id_recurso);


--
-- TOC entry 5293 (class 1259 OID 46568)
-- Name: idx_fila_usuario; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_fila_usuario ON public.tb_fila_execucao USING btree (id_usuario);


--
-- TOC entry 5164 (class 1259 OID 45670)
-- Name: idx_heartbeat_status; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_heartbeat_status ON public.tb_worker_heartbeat USING btree (status);


--
-- TOC entry 5165 (class 1259 OID 45671)
-- Name: idx_heartbeat_ultimo; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_heartbeat_ultimo ON public.tb_worker_heartbeat USING btree (ultimo_heartbeat DESC);


--
-- TOC entry 5157 (class 1259 OID 45645)
-- Name: idx_logs_canal; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_logs_canal ON public.tb_logs_sistema USING btree (canal);


--
-- TOC entry 5170 (class 1259 OID 45692)
-- Name: idx_logs_categoria; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_logs_categoria ON public.logs_sistema USING btree (categoria);


--
-- TOC entry 5171 (class 1259 OID 45693)
-- Name: idx_logs_created_at; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_logs_created_at ON public.logs_sistema USING btree (created_at);


--
-- TOC entry 5158 (class 1259 OID 45644)
-- Name: idx_logs_criado_em; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_logs_criado_em ON public.tb_logs_sistema USING btree (criado_em DESC);


--
-- TOC entry 5152 (class 1259 OID 45629)
-- Name: idx_logs_data_inicio; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_logs_data_inicio ON public.tb_logs_execucao USING btree (data_inicio DESC);


--
-- TOC entry 5159 (class 1259 OID 45643)
-- Name: idx_logs_nivel; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_logs_nivel ON public.tb_logs_sistema USING btree (nivel);


--
-- TOC entry 5153 (class 1259 OID 45630)
-- Name: idx_logs_rotina_data; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_logs_rotina_data ON public.tb_logs_execucao USING btree (id_rotina, data_inicio DESC);


--
-- TOC entry 5154 (class 1259 OID 45628)
-- Name: idx_logs_status; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_logs_status ON public.tb_logs_execucao USING btree (status);


--
-- TOC entry 5210 (class 1259 OID 45892)
-- Name: idx_node_exec_node_id; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_node_exec_node_id ON public.tb_workflow_node_execucoes USING btree (node_id);


--
-- TOC entry 5211 (class 1259 OID 45891)
-- Name: idx_node_exec_status; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_node_exec_status ON public.tb_workflow_node_execucoes USING btree (status);


--
-- TOC entry 5212 (class 1259 OID 45890)
-- Name: idx_node_exec_workflow_exec; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_node_exec_workflow_exec ON public.tb_workflow_node_execucoes USING btree (id_workflow_execucao);


--
-- TOC entry 5227 (class 1259 OID 46322)
-- Name: idx_notificacoes_created; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_notificacoes_created ON public.tb_notificacoes USING btree (created_at DESC);


--
-- TOC entry 5228 (class 1259 OID 46320)
-- Name: idx_notificacoes_lida; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_notificacoes_lida ON public.tb_notificacoes USING btree (lida);


--
-- TOC entry 5229 (class 1259 OID 46321)
-- Name: idx_notificacoes_tipo; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_notificacoes_tipo ON public.tb_notificacoes USING btree (tipo);


--
-- TOC entry 5304 (class 1259 OID 46616)
-- Name: idx_password_resets_token; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_password_resets_token ON public.tb_password_resets USING btree (token_hash);


--
-- TOC entry 5305 (class 1259 OID 46617)
-- Name: idx_password_resets_usuario; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_password_resets_usuario ON public.tb_password_resets USING btree (id_usuario);


--
-- TOC entry 5220 (class 1259 OID 46299)
-- Name: idx_pipe_exec_pipeline; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_pipe_exec_pipeline ON public.tb_pipeline_execucoes USING btree (id_pipeline);


--
-- TOC entry 5221 (class 1259 OID 46300)
-- Name: idx_pipe_exec_status; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_pipe_exec_status ON public.tb_pipeline_execucoes USING btree (status);


--
-- TOC entry 5222 (class 1259 OID 46306)
-- Name: idx_pipeline_exec_data; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_pipeline_exec_data ON public.tb_pipeline_execucoes USING btree (data_inicio);


--
-- TOC entry 5223 (class 1259 OID 46304)
-- Name: idx_pipeline_exec_pipeline; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_pipeline_exec_pipeline ON public.tb_pipeline_execucoes USING btree (id_pipeline);


--
-- TOC entry 5224 (class 1259 OID 46305)
-- Name: idx_pipeline_exec_status; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_pipeline_exec_status ON public.tb_pipeline_execucoes USING btree (status);


--
-- TOC entry 5215 (class 1259 OID 46301)
-- Name: idx_pipelines_ativo; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_pipelines_ativo ON public.tb_pipelines USING btree (ativo);


--
-- TOC entry 5216 (class 1259 OID 46302)
-- Name: idx_pipelines_modo; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_pipelines_modo ON public.tb_pipelines USING btree (modo);


--
-- TOC entry 5217 (class 1259 OID 46303)
-- Name: idx_pipelines_trigger_tipo; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_pipelines_trigger_tipo ON public.tb_pipelines USING btree (trigger_tipo);


--
-- TOC entry 5240 (class 1259 OID 46389)
-- Name: idx_projetos_criador; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_projetos_criador ON public.tb_projetos USING btree (criado_por);


--
-- TOC entry 5241 (class 1259 OID 46388)
-- Name: idx_projetos_empresa; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_projetos_empresa ON public.tb_projetos USING btree (id_empresa);


--
-- TOC entry 5232 (class 1259 OID 46333)
-- Name: idx_rate_limits_chave; Type: INDEX; Schema: public; Owner: postgres
--

CREATE UNIQUE INDEX idx_rate_limits_chave ON public.tb_rate_limits USING btree (chave);


--
-- TOC entry 5258 (class 1259 OID 46450)
-- Name: idx_re_empresa; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_re_empresa ON public.tb_recurso_empresas USING btree (id_empresa);


--
-- TOC entry 5259 (class 1259 OID 46449)
-- Name: idx_re_tipo_recurso; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_re_tipo_recurso ON public.tb_recurso_empresas USING btree (tipo_recurso, id_recurso);


--
-- TOC entry 5145 (class 1259 OID 45627)
-- Name: idx_rotinas_ativa_proxima; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_rotinas_ativa_proxima ON public.tb_rotinas USING btree (ativa, proxima_execucao) WHERE (ativa = true);


--
-- TOC entry 5146 (class 1259 OID 45709)
-- Name: idx_rotinas_datas_ignorar; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_rotinas_datas_ignorar ON public.tb_rotinas USING gin (datas_ignorar_json);


--
-- TOC entry 5147 (class 1259 OID 45708)
-- Name: idx_rotinas_periodo_agendamento; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_rotinas_periodo_agendamento ON public.tb_rotinas USING btree (data_inicio, data_fim) WHERE (agendamento_cron IS NOT NULL);


--
-- TOC entry 5264 (class 1259 OID 46467)
-- Name: idx_rp_projeto; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_rp_projeto ON public.tb_recurso_projetos USING btree (id_projeto);


--
-- TOC entry 5265 (class 1259 OID 46466)
-- Name: idx_rp_tipo_recurso; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_rp_tipo_recurso ON public.tb_recurso_projetos USING btree (tipo_recurso, id_recurso);


--
-- TOC entry 5246 (class 1259 OID 46411)
-- Name: idx_ue_empresa; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_ue_empresa ON public.tb_usuario_empresas USING btree (id_empresa);


--
-- TOC entry 5247 (class 1259 OID 46410)
-- Name: idx_ue_usuario; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_ue_usuario ON public.tb_usuario_empresas USING btree (id_usuario);


--
-- TOC entry 5252 (class 1259 OID 46433)
-- Name: idx_up_projeto; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_up_projeto ON public.tb_usuario_projetos USING btree (id_projeto);


--
-- TOC entry 5253 (class 1259 OID 46432)
-- Name: idx_up_usuario; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_up_usuario ON public.tb_usuario_projetos USING btree (id_usuario);


--
-- TOC entry 5183 (class 1259 OID 45779)
-- Name: idx_valores_capturados_data; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_valores_capturados_data ON public.tb_valores_capturados USING btree (data_captura);


--
-- TOC entry 5184 (class 1259 OID 45777)
-- Name: idx_valores_capturados_evento; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_valores_capturados_evento ON public.tb_valores_capturados USING btree (id_evento);


--
-- TOC entry 5185 (class 1259 OID 45778)
-- Name: idx_valores_capturados_processado; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_valores_capturados_processado ON public.tb_valores_capturados USING btree (processado);


--
-- TOC entry 5198 (class 1259 OID 45842)
-- Name: idx_workflow_edges_destino; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_workflow_edges_destino ON public.tb_workflow_edges USING btree (node_destino);


--
-- TOC entry 5199 (class 1259 OID 45841)
-- Name: idx_workflow_edges_origem; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_workflow_edges_origem ON public.tb_workflow_edges USING btree (node_origem);


--
-- TOC entry 5200 (class 1259 OID 45840)
-- Name: idx_workflow_edges_workflow; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_workflow_edges_workflow ON public.tb_workflow_edges USING btree (id_workflow);


--
-- TOC entry 5205 (class 1259 OID 45870)
-- Name: idx_workflow_exec_data; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_workflow_exec_data ON public.tb_workflow_execucoes USING btree (data_inicio);


--
-- TOC entry 5206 (class 1259 OID 45869)
-- Name: idx_workflow_exec_status; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_workflow_exec_status ON public.tb_workflow_execucoes USING btree (status);


--
-- TOC entry 5207 (class 1259 OID 45868)
-- Name: idx_workflow_exec_workflow; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_workflow_exec_workflow ON public.tb_workflow_execucoes USING btree (id_workflow);


--
-- TOC entry 5192 (class 1259 OID 45820)
-- Name: idx_workflow_nodes_tipo; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_workflow_nodes_tipo ON public.tb_workflow_nodes USING btree (tipo_node);


--
-- TOC entry 5193 (class 1259 OID 45819)
-- Name: idx_workflow_nodes_workflow; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_workflow_nodes_workflow ON public.tb_workflow_nodes USING btree (id_workflow);


--
-- TOC entry 5188 (class 1259 OID 45796)
-- Name: idx_workflows_ativo; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_workflows_ativo ON public.tb_workflows USING btree (ativo);


--
-- TOC entry 5189 (class 1259 OID 45797)
-- Name: idx_workflows_trigger; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_workflows_trigger ON public.tb_workflows USING btree (trigger_tipo);


--
-- TOC entry 5319 (class 2606 OID 45687)
-- Name: logs_sistema fk_logs_usuario; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.logs_sistema
    ADD CONSTRAINT fk_logs_usuario FOREIGN KEY (usuario_id) REFERENCES public.tb_usuarios(id) ON DELETE SET NULL;


--
-- TOC entry 5341 (class 2606 OID 46717)
-- Name: tb_politica_retencao_arquivos fk_politica_rotina; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tb_politica_retencao_arquivos
    ADD CONSTRAINT fk_politica_rotina FOREIGN KEY (id_rotina) REFERENCES public.tb_rotinas(id) ON DELETE CASCADE;


--
-- TOC entry 5313 (class 2606 OID 46502)
-- Name: schedules schedules_criado_por_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.schedules
    ADD CONSTRAINT schedules_criado_por_fkey FOREIGN KEY (criado_por) REFERENCES public.tb_usuarios(id) ON DELETE SET NULL;


--
-- TOC entry 5312 (class 2606 OID 45503)
-- Name: tb_auditoria_rotina tb_auditoria_rotina_id_arquivo_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tb_auditoria_rotina
    ADD CONSTRAINT tb_auditoria_rotina_id_arquivo_fkey FOREIGN KEY (id_arquivo) REFERENCES public.tb_arquivos(id);


--
-- TOC entry 5317 (class 2606 OID 45601)
-- Name: tb_blocos_rotina tb_blocos_rotina_id_rotina_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tb_blocos_rotina
    ADD CONSTRAINT tb_blocos_rotina_id_rotina_fkey FOREIGN KEY (id_rotina) REFERENCES public.tb_rotinas(id) ON DELETE CASCADE;


--
-- TOC entry 5338 (class 2606 OID 46484)
-- Name: tb_compartilhamentos tb_compartilhamentos_id_usuario_destino_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tb_compartilhamentos
    ADD CONSTRAINT tb_compartilhamentos_id_usuario_destino_fkey FOREIGN KEY (id_usuario_destino) REFERENCES public.tb_usuarios(id) ON DELETE CASCADE;


--
-- TOC entry 5339 (class 2606 OID 46479)
-- Name: tb_compartilhamentos tb_compartilhamentos_id_usuario_dono_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tb_compartilhamentos
    ADD CONSTRAINT tb_compartilhamentos_id_usuario_dono_fkey FOREIGN KEY (id_usuario_dono) REFERENCES public.tb_usuarios(id) ON DELETE CASCADE;


--
-- TOC entry 5329 (class 2606 OID 46358)
-- Name: tb_empresas tb_empresas_criado_por_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tb_empresas
    ADD CONSTRAINT tb_empresas_criado_por_fkey FOREIGN KEY (criado_por) REFERENCES public.tb_usuarios(id) ON DELETE SET NULL;


--
-- TOC entry 5320 (class 2606 OID 46497)
-- Name: tb_eventos_api tb_eventos_api_criado_por_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tb_eventos_api
    ADD CONSTRAINT tb_eventos_api_criado_por_fkey FOREIGN KEY (criado_por) REFERENCES public.tb_usuarios(id) ON DELETE SET NULL;


--
-- TOC entry 5321 (class 2606 OID 45747)
-- Name: tb_eventos_api tb_eventos_api_id_api_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tb_eventos_api
    ADD CONSTRAINT tb_eventos_api_id_api_fkey FOREIGN KEY (id_api) REFERENCES public.tb_api_externas(id) ON DELETE CASCADE;


--
-- TOC entry 5318 (class 2606 OID 45615)
-- Name: tb_logs_execucao tb_logs_execucao_id_rotina_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tb_logs_execucao
    ADD CONSTRAINT tb_logs_execucao_id_rotina_fkey FOREIGN KEY (id_rotina) REFERENCES public.tb_rotinas(id) ON DELETE SET NULL;


--
-- TOC entry 5340 (class 2606 OID 46611)
-- Name: tb_password_resets tb_password_resets_id_usuario_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tb_password_resets
    ADD CONSTRAINT tb_password_resets_id_usuario_fkey FOREIGN KEY (id_usuario) REFERENCES public.tb_usuarios(id) ON DELETE CASCADE;


--
-- TOC entry 5314 (class 2606 OID 46492)
-- Name: tb_perfis_conexao tb_perfis_conexao_criado_por_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tb_perfis_conexao
    ADD CONSTRAINT tb_perfis_conexao_criado_por_fkey FOREIGN KEY (criado_por) REFERENCES public.tb_usuarios(id) ON DELETE SET NULL;


--
-- TOC entry 5328 (class 2606 OID 46294)
-- Name: tb_pipeline_execucoes tb_pipeline_execucoes_id_pipeline_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tb_pipeline_execucoes
    ADD CONSTRAINT tb_pipeline_execucoes_id_pipeline_fkey FOREIGN KEY (id_pipeline) REFERENCES public.tb_pipelines(id) ON DELETE CASCADE;


--
-- TOC entry 5330 (class 2606 OID 46383)
-- Name: tb_projetos tb_projetos_criado_por_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tb_projetos
    ADD CONSTRAINT tb_projetos_criado_por_fkey FOREIGN KEY (criado_por) REFERENCES public.tb_usuarios(id) ON DELETE SET NULL;


--
-- TOC entry 5331 (class 2606 OID 46378)
-- Name: tb_projetos tb_projetos_id_empresa_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tb_projetos
    ADD CONSTRAINT tb_projetos_id_empresa_fkey FOREIGN KEY (id_empresa) REFERENCES public.tb_empresas(id) ON DELETE CASCADE;


--
-- TOC entry 5336 (class 2606 OID 46444)
-- Name: tb_recurso_empresas tb_recurso_empresas_id_empresa_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tb_recurso_empresas
    ADD CONSTRAINT tb_recurso_empresas_id_empresa_fkey FOREIGN KEY (id_empresa) REFERENCES public.tb_empresas(id) ON DELETE CASCADE;


--
-- TOC entry 5337 (class 2606 OID 46461)
-- Name: tb_recurso_projetos tb_recurso_projetos_id_projeto_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tb_recurso_projetos
    ADD CONSTRAINT tb_recurso_projetos_id_projeto_fkey FOREIGN KEY (id_projeto) REFERENCES public.tb_projetos(id) ON DELETE CASCADE;


--
-- TOC entry 5315 (class 2606 OID 45581)
-- Name: tb_rotinas tb_rotinas_id_conexao_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tb_rotinas
    ADD CONSTRAINT tb_rotinas_id_conexao_fkey FOREIGN KEY (id_conexao) REFERENCES public.tb_perfis_conexao(id) ON DELETE RESTRICT;


--
-- TOC entry 5316 (class 2606 OID 45586)
-- Name: tb_rotinas tb_rotinas_id_usuario_criador_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tb_rotinas
    ADD CONSTRAINT tb_rotinas_id_usuario_criador_fkey FOREIGN KEY (id_usuario_criador) REFERENCES public.tb_usuarios(id) ON DELETE SET NULL;


--
-- TOC entry 5332 (class 2606 OID 46405)
-- Name: tb_usuario_empresas tb_usuario_empresas_id_empresa_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tb_usuario_empresas
    ADD CONSTRAINT tb_usuario_empresas_id_empresa_fkey FOREIGN KEY (id_empresa) REFERENCES public.tb_empresas(id) ON DELETE CASCADE;


--
-- TOC entry 5333 (class 2606 OID 46400)
-- Name: tb_usuario_empresas tb_usuario_empresas_id_usuario_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tb_usuario_empresas
    ADD CONSTRAINT tb_usuario_empresas_id_usuario_fkey FOREIGN KEY (id_usuario) REFERENCES public.tb_usuarios(id) ON DELETE CASCADE;


--
-- TOC entry 5334 (class 2606 OID 46427)
-- Name: tb_usuario_projetos tb_usuario_projetos_id_projeto_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tb_usuario_projetos
    ADD CONSTRAINT tb_usuario_projetos_id_projeto_fkey FOREIGN KEY (id_projeto) REFERENCES public.tb_projetos(id) ON DELETE CASCADE;


--
-- TOC entry 5335 (class 2606 OID 46422)
-- Name: tb_usuario_projetos tb_usuario_projetos_id_usuario_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tb_usuario_projetos
    ADD CONSTRAINT tb_usuario_projetos_id_usuario_fkey FOREIGN KEY (id_usuario) REFERENCES public.tb_usuarios(id) ON DELETE CASCADE;


--
-- TOC entry 5322 (class 2606 OID 45772)
-- Name: tb_valores_capturados tb_valores_capturados_id_api_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tb_valores_capturados
    ADD CONSTRAINT tb_valores_capturados_id_api_fkey FOREIGN KEY (id_api) REFERENCES public.tb_api_externas(id) ON DELETE CASCADE;


--
-- TOC entry 5323 (class 2606 OID 45767)
-- Name: tb_valores_capturados tb_valores_capturados_id_evento_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tb_valores_capturados
    ADD CONSTRAINT tb_valores_capturados_id_evento_fkey FOREIGN KEY (id_evento) REFERENCES public.tb_eventos_api(id) ON DELETE CASCADE;


--
-- TOC entry 5325 (class 2606 OID 45835)
-- Name: tb_workflow_edges tb_workflow_edges_id_workflow_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tb_workflow_edges
    ADD CONSTRAINT tb_workflow_edges_id_workflow_fkey FOREIGN KEY (id_workflow) REFERENCES public.tb_workflows(id) ON DELETE CASCADE;


--
-- TOC entry 5326 (class 2606 OID 45863)
-- Name: tb_workflow_execucoes tb_workflow_execucoes_id_workflow_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tb_workflow_execucoes
    ADD CONSTRAINT tb_workflow_execucoes_id_workflow_fkey FOREIGN KEY (id_workflow) REFERENCES public.tb_workflows(id) ON DELETE CASCADE;


--
-- TOC entry 5327 (class 2606 OID 45885)
-- Name: tb_workflow_node_execucoes tb_workflow_node_execucoes_id_workflow_execucao_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tb_workflow_node_execucoes
    ADD CONSTRAINT tb_workflow_node_execucoes_id_workflow_execucao_fkey FOREIGN KEY (id_workflow_execucao) REFERENCES public.tb_workflow_execucoes(id) ON DELETE CASCADE;


--
-- TOC entry 5324 (class 2606 OID 45814)
-- Name: tb_workflow_nodes tb_workflow_nodes_id_workflow_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tb_workflow_nodes
    ADD CONSTRAINT tb_workflow_nodes_id_workflow_fkey FOREIGN KEY (id_workflow) REFERENCES public.tb_workflows(id) ON DELETE CASCADE;


-- Completed on 2026-03-27 16:58:28

--
-- PostgreSQL database dump complete
--

