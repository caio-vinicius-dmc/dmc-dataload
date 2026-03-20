--
-- PostgreSQL database dump
--

-- Dumped from database version 17.5
-- Dumped by pg_dump version 17.5

-- Started on 2026-03-20 17:57:13

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
-- TOC entry 5512 (class 0 OID 0)
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
-- TOC entry 5513 (class 0 OID 0)
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
-- TOC entry 5514 (class 0 OID 0)
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
-- TOC entry 5515 (class 0 OID 0)
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
-- TOC entry 5516 (class 0 OID 0)
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
-- TOC entry 5517 (class 0 OID 0)
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
-- TOC entry 5518 (class 0 OID 0)
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
-- TOC entry 5519 (class 0 OID 0)
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
-- TOC entry 5520 (class 0 OID 0)
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
-- TOC entry 5521 (class 0 OID 0)
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
-- TOC entry 5522 (class 0 OID 0)
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
-- TOC entry 5523 (class 0 OID 0)
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
-- TOC entry 5524 (class 0 OID 0)
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
-- TOC entry 5525 (class 0 OID 0)
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
-- TOC entry 5526 (class 0 OID 0)
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
-- TOC entry 5527 (class 0 OID 0)
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
-- TOC entry 5528 (class 0 OID 0)
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
-- TOC entry 5529 (class 0 OID 0)
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
-- TOC entry 5530 (class 0 OID 0)
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
-- TOC entry 5531 (class 0 OID 0)
-- Dependencies: 260
-- Name: tb_pipelines_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.tb_pipelines_id_seq OWNED BY public.tb_pipelines.id;


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
-- TOC entry 5532 (class 0 OID 0)
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
-- TOC entry 5533 (class 0 OID 0)
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
-- TOC entry 5534 (class 0 OID 0)
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
-- TOC entry 5535 (class 0 OID 0)
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
    notificar_falha boolean DEFAULT true
);


ALTER TABLE public.tb_rotinas OWNER TO postgres;

--
-- TOC entry 5536 (class 0 OID 0)
-- Dependencies: 230
-- Name: COLUMN tb_rotinas.data_inicio; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.tb_rotinas.data_inicio IS 'Data e hora de início do agendamento (quando começar a executar)';


--
-- TOC entry 5537 (class 0 OID 0)
-- Dependencies: 230
-- Name: COLUMN tb_rotinas.data_fim; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.tb_rotinas.data_fim IS 'Data e hora de término do agendamento (quando parar de executar)';


--
-- TOC entry 5538 (class 0 OID 0)
-- Dependencies: 230
-- Name: COLUMN tb_rotinas.datas_ignorar_json; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.tb_rotinas.datas_ignorar_json IS 'Array JSON com datas específicas para não executar';


--
-- TOC entry 5539 (class 0 OID 0)
-- Dependencies: 230
-- Name: COLUMN tb_rotinas.ignorar_feriados; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.tb_rotinas.ignorar_feriados IS 'Se deve ignorar feriados nacionais brasileiros';


--
-- TOC entry 5540 (class 0 OID 0)
-- Dependencies: 230
-- Name: COLUMN tb_rotinas.timeout; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.tb_rotinas.timeout IS 'Timeout máximo de execução em segundos (padrão: 300s = 5min)';


--
-- TOC entry 5541 (class 0 OID 0)
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
-- TOC entry 5542 (class 0 OID 0)
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
-- TOC entry 5543 (class 0 OID 0)
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
-- TOC entry 5544 (class 0 OID 0)
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
-- TOC entry 5545 (class 0 OID 0)
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
-- TOC entry 5546 (class 0 OID 0)
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
-- TOC entry 5547 (class 0 OID 0)
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
-- TOC entry 5548 (class 0 OID 0)
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
-- TOC entry 5549 (class 0 OID 0)
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
-- TOC entry 5550 (class 0 OID 0)
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
-- TOC entry 5551 (class 0 OID 0)
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
-- TOC entry 5552 (class 0 OID 0)
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
-- TOC entry 5553 (class 0 OID 0)
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
-- TOC entry 5554 (class 0 OID 0)
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
-- TOC entry 5555 (class 0 OID 0)
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
-- TOC entry 5556 (class 0 OID 0)
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
-- TOC entry 5557 (class 0 OID 0)
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
-- TOC entry 4900 (class 2604 OID 45523)
-- Name: connections id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.connections ALTER COLUMN id SET DEFAULT nextval('public.connections_id_seq'::regclass);


--
-- TOC entry 4934 (class 2604 OID 45680)
-- Name: logs_sistema id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.logs_sistema ALTER COLUMN id SET DEFAULT nextval('public.logs_sistema_id_seq'::regclass);


--
-- TOC entry 4901 (class 2604 OID 45532)
-- Name: schedules id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.schedules ALTER COLUMN id SET DEFAULT nextval('public.schedules_id_seq'::regclass);


--
-- TOC entry 4937 (class 2604 OID 45714)
-- Name: tb_api_externas id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tb_api_externas ALTER COLUMN id SET DEFAULT nextval('public.tb_api_externas_id_seq'::regclass);


--
-- TOC entry 4897 (class 2604 OID 45488)
-- Name: tb_arquivos id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tb_arquivos ALTER COLUMN id SET DEFAULT nextval('public.tb_arquivos_id_seq'::regclass);


--
-- TOC entry 5045 (class 2604 OID 46511)
-- Name: tb_auditoria id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tb_auditoria ALTER COLUMN id SET DEFAULT nextval('public.tb_auditoria_id_seq'::regclass);


--
-- TOC entry 4899 (class 2604 OID 45498)
-- Name: tb_auditoria_rotina id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tb_auditoria_rotina ALTER COLUMN id SET DEFAULT nextval('public.tb_auditoria_rotina_id_seq'::regclass);


--
-- TOC entry 5072 (class 2604 OID 46591)
-- Name: tb_backups id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tb_backups ALTER COLUMN id SET DEFAULT nextval('public.tb_backups_id_seq'::regclass);


--
-- TOC entry 5064 (class 2604 OID 46573)
-- Name: tb_canais_notificacao id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tb_canais_notificacao ALTER COLUMN id SET DEFAULT nextval('public.tb_canais_notificacao_id_seq'::regclass);


--
-- TOC entry 5042 (class 2604 OID 46472)
-- Name: tb_compartilhamentos id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tb_compartilhamentos ALTER COLUMN id SET DEFAULT nextval('public.tb_compartilhamentos_id_seq'::regclass);


--
-- TOC entry 5026 (class 2604 OID 46348)
-- Name: tb_empresas id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tb_empresas ALTER COLUMN id SET DEFAULT nextval('public.tb_empresas_id_seq'::regclass);


--
-- TOC entry 4948 (class 2604 OID 45735)
-- Name: tb_eventos_api id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tb_eventos_api ALTER COLUMN id SET DEFAULT nextval('public.tb_eventos_api_id_seq'::regclass);


--
-- TOC entry 5057 (class 2604 OID 46553)
-- Name: tb_fila_execucao id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tb_fila_execucao ALTER COLUMN id SET DEFAULT nextval('public.tb_fila_execucao_id_seq'::regclass);


--
-- TOC entry 4922 (class 2604 OID 45635)
-- Name: tb_logs_sistema id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tb_logs_sistema ALTER COLUMN id SET DEFAULT nextval('public.tb_logs_sistema_id_seq'::regclass);


--
-- TOC entry 4926 (class 2604 OID 45650)
-- Name: tb_metricas_sistema id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tb_metricas_sistema ALTER COLUMN id SET DEFAULT nextval('public.tb_metricas_sistema_id_seq'::regclass);


--
-- TOC entry 5017 (class 2604 OID 46311)
-- Name: tb_notificacoes id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tb_notificacoes ALTER COLUMN id SET DEFAULT nextval('public.tb_notificacoes_id_seq'::regclass);


--
-- TOC entry 5008 (class 2604 OID 46281)
-- Name: tb_pipeline_execucoes id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tb_pipeline_execucoes ALTER COLUMN id SET DEFAULT nextval('public.tb_pipeline_execucoes_id_seq'::regclass);


--
-- TOC entry 4996 (class 2604 OID 46261)
-- Name: tb_pipelines id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tb_pipelines ALTER COLUMN id SET DEFAULT nextval('public.tb_pipelines_id_seq'::regclass);


--
-- TOC entry 5030 (class 2604 OID 46368)
-- Name: tb_projetos id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tb_projetos ALTER COLUMN id SET DEFAULT nextval('public.tb_projetos_id_seq'::regclass);


--
-- TOC entry 5022 (class 2604 OID 46327)
-- Name: tb_rate_limits id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tb_rate_limits ALTER COLUMN id SET DEFAULT nextval('public.tb_rate_limits_id_seq'::regclass);


--
-- TOC entry 5038 (class 2604 OID 46438)
-- Name: tb_recurso_empresas id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tb_recurso_empresas ALTER COLUMN id SET DEFAULT nextval('public.tb_recurso_empresas_id_seq'::regclass);


--
-- TOC entry 5040 (class 2604 OID 46455)
-- Name: tb_recurso_projetos id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tb_recurso_projetos ALTER COLUMN id SET DEFAULT nextval('public.tb_recurso_projetos_id_seq'::regclass);


--
-- TOC entry 5034 (class 2604 OID 46394)
-- Name: tb_usuario_empresas id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tb_usuario_empresas ALTER COLUMN id SET DEFAULT nextval('public.tb_usuario_empresas_id_seq'::regclass);


--
-- TOC entry 5036 (class 2604 OID 46416)
-- Name: tb_usuario_projetos id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tb_usuario_projetos ALTER COLUMN id SET DEFAULT nextval('public.tb_usuario_projetos_id_seq'::regclass);


--
-- TOC entry 4956 (class 2604 OID 45759)
-- Name: tb_valores_capturados id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tb_valores_capturados ALTER COLUMN id SET DEFAULT nextval('public.tb_valores_capturados_id_seq'::regclass);


--
-- TOC entry 5051 (class 2604 OID 46538)
-- Name: tb_webhooks id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tb_webhooks ALTER COLUMN id SET DEFAULT nextval('public.tb_webhooks_id_seq'::regclass);


--
-- TOC entry 4929 (class 2604 OID 45661)
-- Name: tb_worker_heartbeat id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tb_worker_heartbeat ALTER COLUMN id SET DEFAULT nextval('public.tb_worker_heartbeat_id_seq'::regclass);


--
-- TOC entry 4974 (class 2604 OID 45825)
-- Name: tb_workflow_edges id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tb_workflow_edges ALTER COLUMN id SET DEFAULT nextval('public.tb_workflow_edges_id_seq'::regclass);


--
-- TOC entry 4978 (class 2604 OID 45847)
-- Name: tb_workflow_execucoes id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tb_workflow_execucoes ALTER COLUMN id SET DEFAULT nextval('public.tb_workflow_execucoes_id_seq'::regclass);


--
-- TOC entry 4990 (class 2604 OID 45875)
-- Name: tb_workflow_node_execucoes id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tb_workflow_node_execucoes ALTER COLUMN id SET DEFAULT nextval('public.tb_workflow_node_execucoes_id_seq'::regclass);


--
-- TOC entry 4968 (class 2604 OID 45802)
-- Name: tb_workflow_nodes id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tb_workflow_nodes ALTER COLUMN id SET DEFAULT nextval('public.tb_workflow_nodes_id_seq'::regclass);


--
-- TOC entry 4960 (class 2604 OID 45784)
-- Name: tb_workflows id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tb_workflows ALTER COLUMN id SET DEFAULT nextval('public.tb_workflows_id_seq'::regclass);


--
-- TOC entry 5435 (class 0 OID 45520)
-- Dependencies: 222
-- Data for Name: connections; Type: TABLE DATA; Schema: public; Owner: postgres
--

INSERT INTO public.connections VALUES (1, 'local_sqlite', 'sqlite', NULL, '', 'C:\Users\caio.barros\OneDrive\Cloud\PESSOAL\CAIO\NOTEBOOK\PROJETOS\DMC-DATALOAD\backend\test_target.db', NULL, NULL, '', '{"driver": "sqlite", "database": "C:\\Users\\caio.barros\\OneDrive\\Cloud\\PESSOAL\\CAIO\\NOTEBOOK\\PROJETOS\\DMC-DATALOAD\\backend\\test_target.db"}');


--
-- TOC entry 5455 (class 0 OID 45677)
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
-- TOC entry 5437 (class 0 OID 45529)
-- Dependencies: 224
-- Data for Name: schedules; Type: TABLE DATA; Schema: public; Owner: postgres
--



--
-- TOC entry 5457 (class 0 OID 45711)
-- Dependencies: 245
-- Data for Name: tb_api_externas; Type: TABLE DATA; Schema: public; Owner: postgres
--

INSERT INTO public.tb_api_externas VALUES (1, 'JSONPlaceholder - Posts', 'API de teste para verificar posts', 'https://jsonplaceholder.typicode.com/posts/1', 'GET', '{}', 'none', '{}', NULL, 'json', 30, 30, true, NULL, NULL, NULL, 1, '2026-02-04 16:35:46.543643-03', '2026-02-04 16:35:46.543643-03');
INSERT INTO public.tb_api_externas VALUES (2, 'JSONPlaceholder - Users', 'API de teste para verificar usuários', 'https://jsonplaceholder.typicode.com/users', 'GET', '{}', 'none', '{}', NULL, 'json', 60, 30, true, NULL, NULL, NULL, 1, '2026-02-04 16:35:46.543643-03', '2026-02-04 16:35:46.543643-03');
INSERT INTO public.tb_api_externas VALUES (3, 'JSONPlaceholder - Posts', 'API pública para testes', 'https://jsonplaceholder.typicode.com/posts', 'GET', '{}', 'none', '{}', NULL, 'json', 60, 30, true, NULL, NULL, NULL, 1, '2026-02-04 17:00:25.562939-03', '2026-02-04 17:00:25.562939-03');
INSERT INTO public.tb_api_externas VALUES (4, 'JSONPlaceholder - Users', 'API de usuários para testes', 'https://jsonplaceholder.typicode.com/users', 'GET', '{}', 'none', '{}', NULL, 'json', 120, 30, true, NULL, NULL, NULL, 1, '2026-02-04 17:00:25.566021-03', '2026-02-04 17:00:25.566021-03');
INSERT INTO public.tb_api_externas VALUES (19, 'API_JSONPlaceholder_Users', NULL, 'https://jsonplaceholder.typicode.com/users', 'GET', '[]', 'none', '[]', NULL, 'json', 600, 30, true, NULL, NULL, NULL, 1, '2026-03-19 13:34:01.087637-03', '2026-03-19 13:34:01.087637-03');
INSERT INTO public.tb_api_externas VALUES (20, 'API_JSONPlaceholder_Comments', NULL, 'https://jsonplaceholder.typicode.com/comments', 'GET', '[]', 'none', '[]', NULL, 'json', 900, 30, true, NULL, NULL, NULL, 1, '2026-03-19 13:34:04.541924-03', '2026-03-19 13:34:04.541924-03');
INSERT INTO public.tb_api_externas VALUES (21, 'API_JSONPlaceholder_Todos', NULL, 'https://jsonplaceholder.typicode.com/todos', 'GET', '[]', 'none', '[]', NULL, 'json', 120, 30, true, NULL, NULL, NULL, 1, '2026-03-19 13:34:04.738782-03', '2026-03-19 13:34:04.738782-03');
INSERT INTO public.tb_api_externas VALUES (22, 'API_JSONPlaceholder_Albums', NULL, 'https://jsonplaceholder.typicode.com/albums', 'GET', '[]', 'none', '[]', NULL, 'json', 1800, 30, true, NULL, NULL, NULL, 1, '2026-03-19 13:34:05.023351-03', '2026-03-19 13:34:05.023351-03');
INSERT INTO public.tb_api_externas VALUES (23, 'API_JSONPlaceholder_Comments', NULL, 'https://jsonplaceholder.typicode.com/comments', 'GET', '[]', 'none', '[]', NULL, 'json', 900, 30, true, NULL, NULL, NULL, 1, '2026-03-19 13:34:07.97545-03', '2026-03-19 13:34:07.97545-03');
INSERT INTO public.tb_api_externas VALUES (24, 'API_JSONPlaceholder_Todos', NULL, 'https://jsonplaceholder.typicode.com/todos', 'GET', '[]', 'none', '[]', NULL, 'json', 120, 30, true, NULL, NULL, NULL, 1, '2026-03-19 13:34:08.097116-03', '2026-03-19 13:34:08.097116-03');
INSERT INTO public.tb_api_externas VALUES (25, 'API_JSONPlaceholder_Albums', NULL, 'https://jsonplaceholder.typicode.com/albums', 'GET', '[]', 'none', '[]', NULL, 'json', 1800, 30, true, NULL, NULL, NULL, 1, '2026-03-19 13:34:08.225291-03', '2026-03-19 13:34:08.225291-03');
INSERT INTO public.tb_api_externas VALUES (18, 'API_JSONPlaceholder_Posts', NULL, 'https://jsonplaceholder.typicode.com/posts', 'GET', '[]', 'none', '[]', NULL, 'json', 300, 30, true, '2026-03-19 13:39:08.158726-03', '200', NULL, 1, '2026-03-19 13:33:54.583887-03', '2026-03-19 13:33:54.583887-03');


--
-- TOC entry 5431 (class 0 OID 45485)
-- Dependencies: 218
-- Data for Name: tb_arquivos; Type: TABLE DATA; Schema: public; Owner: postgres
--

INSERT INTO public.tb_arquivos VALUES (1, 'dt_b1_20260130T120315_04b20c4be64b40f19e6ef66216200c1b.csv', 66, '\x69643b6e616d650d0a313b610d0a323b620d0a333b630d0a343b630d0a353b630d0a363b630d0a373b630d0a383b630d0a393b630d0a31303b630d0a31313b630d0a', '2026-01-30 09:03:15.786811-03', 'dt', 'b1');
INSERT INTO public.tb_arquivos VALUES (2, 't1_b1_20260130T120322_c378c6092e074789a55086478283a334.csv', 66, '\x69643b6e616d650d0a313b610d0a323b620d0a333b630d0a343b630d0a353b630d0a363b630d0a373b630d0a383b630d0a393b630d0a31303b630d0a31313b630d0a', '2026-01-30 09:03:22.509801-03', 't1', 'b1');
INSERT INTO public.tb_arquivos VALUES (3, 'sch_b_20260130T120327_fe62995b8c6e4aa58a877b7dc5f4b85c.csv', 72, '\x69643b6e616d650d0a313b610d0a323b620d0a333b630d0a343b630d0a353b630d0a363b630d0a373b630d0a383b630d0a393b630d0a31303b630d0a31313b630d0a31323b630d0a', '2026-01-30 09:03:27.586165-03', 'sch', 'b');


--
-- TOC entry 5495 (class 0 OID 46508)
-- Dependencies: 283
-- Data for Name: tb_auditoria; Type: TABLE DATA; Schema: public; Owner: postgres
--

INSERT INTO public.tb_auditoria VALUES (1, 'login', 'sessao', 1, 'admin', 1, 'admin', 'super_admin', '[]', '[]', '::1', '', '2026-03-19 19:35:09.370679-03');
INSERT INTO public.tb_auditoria VALUES (2, 'login', 'sessao', 1, 'admin', 1, 'admin', 'super_admin', '[]', '[]', '::1', '', '2026-03-19 19:35:13.285589-03');
INSERT INTO public.tb_auditoria VALUES (3, 'login', 'sessao', 8, 'test_dev', 8, 'test_dev', 'desenvolvedor', '[]', '[]', '::1', '', '2026-03-19 19:35:13.494151-03');
INSERT INTO public.tb_auditoria VALUES (4, 'login', 'sessao', 9, 'test_op', 9, 'test_op', 'operador', '[]', '[]', '::1', '', '2026-03-19 19:35:13.699215-03');
INSERT INTO public.tb_auditoria VALUES (5, 'login', 'sessao', 1, 'admin', 1, 'admin', 'super_admin', '[]', '[]', '::1', '', '2026-03-19 19:35:39.389226-03');
INSERT INTO public.tb_auditoria VALUES (6, 'editar', 'configuracao', NULL, 'Grupo: geral', 1, 'admin', 'super_admin', '{"app_nome": "DMC DataLoad"}', '{"app_nome": "DMC DataLoad"}', '::1', '', '2026-03-19 19:35:41.462974-03');
INSERT INTO public.tb_auditoria VALUES (7, 'login', 'sessao', 1, 'admin', 1, 'admin', 'super_admin', '[]', '[]', '::1', '', '2026-03-19 19:36:24.22956-03');
INSERT INTO public.tb_auditoria VALUES (8, 'editar', 'configuracao', NULL, 'Grupo: geral', 1, 'admin', 'super_admin', '{"app_nome": "DMC DataLoad"}', '{"app_nome": "DMC DataLoad"}', '::1', '', '2026-03-19 19:36:26.474423-03');
INSERT INTO public.tb_auditoria VALUES (9, 'login', 'sessao', 1, 'admin', 1, 'admin', 'super_admin', '[]', '[]', '::1', '', '2026-03-19 19:36:36.94199-03');
INSERT INTO public.tb_auditoria VALUES (10, 'editar', 'configuracao', NULL, 'Grupo: geral', 1, 'admin', 'super_admin', '{"app_nome": "DMC DataLoad"}', '{"app_nome": "DMC DataLoad"}', '::1', '', '2026-03-19 19:36:39.424102-03');
INSERT INTO public.tb_auditoria VALUES (11, 'login', 'sessao', 1, 'admin', 1, 'admin', 'super_admin', '[]', '[]', '::1', '', '2026-03-19 19:37:10.042154-03');
INSERT INTO public.tb_auditoria VALUES (12, 'criar', 'pipeline', 14, 'Pipeline Teste E2E', 1, 'admin', 'super_admin', '[]', '[]', '::1', '', '2026-03-19 19:37:11.944499-03');
INSERT INTO public.tb_auditoria VALUES (13, 'excluir', 'pipeline', 14, NULL, 1, 'admin', 'super_admin', '[]', '[]', '::1', '', '2026-03-19 19:37:13.08278-03');
INSERT INTO public.tb_auditoria VALUES (14, 'criar', 'workflow', 0, 'Workflow Teste E2E', 1, 'admin', 'super_admin', '[]', '[]', '::1', '', '2026-03-19 19:37:14.676793-03');
INSERT INTO public.tb_auditoria VALUES (15, 'login', 'sessao', 1, 'admin', 1, 'admin', 'super_admin', '[]', '[]', '::1', '', '2026-03-19 19:37:27.054643-03');
INSERT INTO public.tb_auditoria VALUES (16, 'criar', 'pipeline', 15, 'Pipeline Teste E2E', 1, 'admin', 'super_admin', '[]', '[]', '::1', '', '2026-03-19 19:37:28.920669-03');
INSERT INTO public.tb_auditoria VALUES (17, 'excluir', 'pipeline', 15, NULL, 1, 'admin', 'super_admin', '[]', '[]', '::1', '', '2026-03-19 19:37:30.471738-03');
INSERT INTO public.tb_auditoria VALUES (18, 'criar', 'workflow', 0, 'Workflow Teste E2E', 1, 'admin', 'super_admin', '[]', '[]', '::1', '', '2026-03-19 19:37:31.89306-03');
INSERT INTO public.tb_auditoria VALUES (19, 'login', 'sessao', 1, 'admin', 1, 'admin', 'super_admin', '[]', '[]', '::1', '', '2026-03-19 19:41:48.028484-03');
INSERT INTO public.tb_auditoria VALUES (20, 'login', 'sessao', 1, 'admin', 1, 'admin', 'super_admin', '[]', '[]', '::1', '', '2026-03-19 19:42:03.720657-03');
INSERT INTO public.tb_auditoria VALUES (21, 'criar', 'workflow', 0, 'WF Test Debug', 1, 'admin', 'super_admin', '[]', '[]', '::1', '', '2026-03-19 19:42:05.00475-03');
INSERT INTO public.tb_auditoria VALUES (22, 'login', 'sessao', 1, 'admin', 1, 'admin', 'super_admin', '[]', '[]', '::1', '', '2026-03-19 19:42:26.300745-03');
INSERT INTO public.tb_auditoria VALUES (23, 'criar', 'pipeline', 16, 'Pipeline Teste E2E', 1, 'admin', 'super_admin', '[]', '[]', '::1', '', '2026-03-19 19:42:28.516308-03');
INSERT INTO public.tb_auditoria VALUES (24, 'excluir', 'pipeline', 16, NULL, 1, 'admin', 'super_admin', '[]', '[]', '::1', '', '2026-03-19 19:42:29.576804-03');
INSERT INTO public.tb_auditoria VALUES (25, 'criar', 'workflow', 8, 'Workflow Teste E2E', 1, 'admin', 'super_admin', '[]', '[]', '::1', '', '2026-03-19 19:42:30.718115-03');
INSERT INTO public.tb_auditoria VALUES (26, 'excluir', 'workflow', 8, NULL, 1, 'admin', 'super_admin', '[]', '[]', '::1', '', '2026-03-19 19:42:32.851943-03');
INSERT INTO public.tb_auditoria VALUES (27, 'login', 'sessao', 1, 'admin', 1, 'admin', 'super_admin', '[]', '[]', '::1', '', '2026-03-19 19:45:33.924703-03');
INSERT INTO public.tb_auditoria VALUES (28, 'login', 'sessao', 1, 'admin', 1, 'admin', 'super_admin', '[]', '[]', '::1', '', '2026-03-19 19:45:35.745903-03');
INSERT INTO public.tb_auditoria VALUES (29, 'login', 'sessao', 10, 'test_dev', 10, 'test_dev', 'desenvolvedor', '[]', '[]', '::1', '', '2026-03-19 19:45:35.920925-03');
INSERT INTO public.tb_auditoria VALUES (30, 'login', 'sessao', 11, 'test_op', 11, 'test_op', 'operador', '[]', '[]', '::1', '', '2026-03-19 19:45:36.086024-03');
INSERT INTO public.tb_auditoria VALUES (31, 'login', 'sessao', 1, 'admin', 1, 'admin', 'super_admin', '[]', '[]', '::1', '', '2026-03-19 19:45:40.05858-03');
INSERT INTO public.tb_auditoria VALUES (32, 'editar', 'configuracao', NULL, 'Grupo: geral', 1, 'admin', 'super_admin', '{"app_nome": "DMC DataLoad"}', '{"app_nome": "DMC DataLoad"}', '::1', '', '2026-03-19 19:45:42.616994-03');
INSERT INTO public.tb_auditoria VALUES (33, 'login', 'sessao', 1, 'admin', 1, 'admin', 'super_admin', '[]', '[]', '::1', '', '2026-03-19 19:45:48.48695-03');
INSERT INTO public.tb_auditoria VALUES (34, 'criar', 'pipeline', 17, 'Pipeline Teste E2E', 1, 'admin', 'super_admin', '[]', '[]', '::1', '', '2026-03-19 19:45:50.499075-03');
INSERT INTO public.tb_auditoria VALUES (35, 'excluir', 'pipeline', 17, NULL, 1, 'admin', 'super_admin', '[]', '[]', '::1', '', '2026-03-19 19:45:51.814724-03');
INSERT INTO public.tb_auditoria VALUES (36, 'criar', 'workflow', 9, 'Workflow Teste E2E', 1, 'admin', 'super_admin', '[]', '[]', '::1', '', '2026-03-19 19:45:53.787388-03');
INSERT INTO public.tb_auditoria VALUES (37, 'excluir', 'workflow', 9, NULL, 1, 'admin', 'super_admin', '[]', '[]', '::1', '', '2026-03-19 19:45:56.074156-03');
INSERT INTO public.tb_auditoria VALUES (38, 'login', 'sessao', 1, 'admin', 1, 'admin', 'super_admin', '[]', '[]', '::1', '', '2026-03-19 19:56:39.013341-03');
INSERT INTO public.tb_auditoria VALUES (39, 'criar', 'usuario', 12, 'test_assoc_1773960999', 1, 'admin', 'super_admin', '[]', '{"nivel_acesso": "operador"}', '::1', '', '2026-03-19 19:56:39.627586-03');
INSERT INTO public.tb_auditoria VALUES (40, 'editar', 'usuario', 12, 'test_assoc_1773960999', 1, 'admin', 'super_admin', '[]', '{"nivel_acesso": "operador"}', '::1', '', '2026-03-19 19:56:39.874595-03');
INSERT INTO public.tb_auditoria VALUES (41, 'excluir', 'usuario', 12, 'test_assoc_1773960999', 1, 'admin', 'super_admin', '[]', '[]', '::1', '', '2026-03-19 19:56:40.303378-03');
INSERT INTO public.tb_auditoria VALUES (42, 'editar', 'usuario', 3, 'leo', 1, 'admin', 'super_admin', '[]', '{"nivel_acesso": "desenvolvedor"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-19 20:05:53.0407-03');
INSERT INTO public.tb_auditoria VALUES (43, 'criar', 'usuario', 13, 'Caio', 1, 'admin', 'super_admin', '[]', '{"nivel_acesso": "desenvolvedor"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-19 20:06:16.075994-03');
INSERT INTO public.tb_auditoria VALUES (44, 'login', 'sessao', 1, 'admin', 1, 'admin', 'super_admin', '[]', '[]', '::1', '', '2026-03-19 20:07:30.998196-03');
INSERT INTO public.tb_auditoria VALUES (45, 'login', 'sessao', 1, 'admin', 1, 'admin', 'super_admin', '[]', '[]', '::1', '', '2026-03-19 20:07:34.939935-03');
INSERT INTO public.tb_auditoria VALUES (46, 'login', 'sessao', 14, 'test_dev', 14, 'test_dev', 'desenvolvedor', '[]', '[]', '::1', '', '2026-03-19 20:07:35.139987-03');
INSERT INTO public.tb_auditoria VALUES (47, 'login', 'sessao', 15, 'test_op', 15, 'test_op', 'operador', '[]', '[]', '::1', '', '2026-03-19 20:07:35.348165-03');
INSERT INTO public.tb_auditoria VALUES (48, 'login', 'sessao', 1, 'admin', 1, 'admin', 'super_admin', '[]', '[]', '::1', '', '2026-03-19 20:07:42.769413-03');
INSERT INTO public.tb_auditoria VALUES (49, 'editar', 'configuracao', NULL, 'Grupo: geral', 1, 'admin', 'super_admin', '{"app_nome": "DMC DataLoad"}', '{"app_nome": "DMC DataLoad"}', '::1', '', '2026-03-19 20:07:47.100101-03');
INSERT INTO public.tb_auditoria VALUES (50, 'logout', 'sessao', NULL, NULL, 1, 'admin', 'super_admin', '[]', '[]', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-19 20:07:54.438611-03');
INSERT INTO public.tb_auditoria VALUES (51, 'login', 'sessao', 1, 'admin', 1, 'admin', 'super_admin', '[]', '[]', '::1', '', '2026-03-19 20:07:55.91775-03');
INSERT INTO public.tb_auditoria VALUES (52, 'criar', 'pipeline', 18, 'Pipeline Teste E2E', 1, 'admin', 'super_admin', '[]', '[]', '::1', '', '2026-03-19 20:07:58.3-03');
INSERT INTO public.tb_auditoria VALUES (53, 'excluir', 'pipeline', 18, NULL, 1, 'admin', 'super_admin', '[]', '[]', '::1', '', '2026-03-19 20:07:59.506212-03');
INSERT INTO public.tb_auditoria VALUES (54, 'criar', 'workflow', 10, 'Workflow Teste E2E', 1, 'admin', 'super_admin', '[]', '[]', '::1', '', '2026-03-19 20:08:00.797285-03');
INSERT INTO public.tb_auditoria VALUES (55, 'login', 'sessao', 1, 'admin', 1, 'admin', 'super_admin', '[]', '[]', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-19 20:08:01.833917-03');
INSERT INTO public.tb_auditoria VALUES (56, 'excluir', 'workflow', 10, NULL, 1, 'admin', 'super_admin', '[]', '[]', '::1', '', '2026-03-19 20:08:03.466026-03');
INSERT INTO public.tb_auditoria VALUES (57, 'login', 'sessao', 1, 'admin', 1, 'admin', 'super_admin', '[]', '[]', '::1', '', '2026-03-19 20:09:24.809823-03');
INSERT INTO public.tb_auditoria VALUES (58, 'login', 'sessao', 1, 'admin', 1, 'admin', 'super_admin', '[]', '[]', '::1', '', '2026-03-19 20:09:45.364502-03');
INSERT INTO public.tb_auditoria VALUES (59, 'login', 'sessao', 1, 'admin', 1, 'admin', 'super_admin', '[]', '[]', '::1', '', '2026-03-19 20:10:02.686081-03');
INSERT INTO public.tb_auditoria VALUES (60, 'login', 'sessao', 1, 'admin', 1, 'admin', 'super_admin', '[]', '[]', '::1', '', '2026-03-19 20:10:16.851534-03');
INSERT INTO public.tb_auditoria VALUES (61, 'login', 'sessao', 1, 'admin', 1, 'admin', 'super_admin', '[]', '[]', '::1', '', '2026-03-19 20:10:42.108874-03');
INSERT INTO public.tb_auditoria VALUES (62, 'login', 'sessao', 1, 'admin', 1, 'admin', 'super_admin', '[]', '[]', '::1', '', '2026-03-19 20:10:56.175283-03');
INSERT INTO public.tb_auditoria VALUES (63, 'login', 'sessao', 1, 'admin', 1, 'admin', 'super_admin', '[]', '[]', '::1', '', '2026-03-19 20:11:05.424539-03');
INSERT INTO public.tb_auditoria VALUES (64, 'login', 'sessao', 1, 'admin', 1, 'admin', 'super_admin', '[]', '[]', '::1', '', '2026-03-19 20:11:30.763539-03');
INSERT INTO public.tb_auditoria VALUES (65, 'login', 'sessao', 1, 'admin', 1, 'admin', 'super_admin', '[]', '[]', '::1', '', '2026-03-19 20:11:41.772262-03');
INSERT INTO public.tb_auditoria VALUES (66, 'login', 'sessao', 1, 'admin', 1, 'admin', 'super_admin', '[]', '[]', '::1', '', '2026-03-19 20:11:45.282775-03');
INSERT INTO public.tb_auditoria VALUES (67, 'login', 'sessao', 16, 'test_dev', 16, 'test_dev', 'desenvolvedor', '[]', '[]', '::1', '', '2026-03-19 20:11:45.463437-03');
INSERT INTO public.tb_auditoria VALUES (68, 'login', 'sessao', 17, 'test_op', 17, 'test_op', 'operador', '[]', '[]', '::1', '', '2026-03-19 20:11:45.635869-03');
INSERT INTO public.tb_auditoria VALUES (69, 'login', 'sessao', 1, 'admin', 1, 'admin', 'super_admin', '[]', '[]', '::1', '', '2026-03-19 20:11:52.03937-03');
INSERT INTO public.tb_auditoria VALUES (70, 'editar', 'configuracao', NULL, 'Grupo: geral', 1, 'admin', 'super_admin', '{"app_nome": "DMC DataLoad"}', '{"app_nome": "DMC DataLoad"}', '::1', '', '2026-03-19 20:11:55.574667-03');
INSERT INTO public.tb_auditoria VALUES (71, 'login', 'sessao', 1, 'admin', 1, 'admin', 'super_admin', '[]', '[]', '::1', '', '2026-03-19 20:12:02.775903-03');
INSERT INTO public.tb_auditoria VALUES (72, 'criar', 'pipeline', 19, 'Pipeline Teste E2E', 1, 'admin', 'super_admin', '[]', '[]', '::1', '', '2026-03-19 20:12:05.088389-03');
INSERT INTO public.tb_auditoria VALUES (73, 'excluir', 'pipeline', 19, NULL, 1, 'admin', 'super_admin', '[]', '[]', '::1', '', '2026-03-19 20:12:06.262074-03');
INSERT INTO public.tb_auditoria VALUES (74, 'criar', 'workflow', 11, 'Workflow Teste E2E', 1, 'admin', 'super_admin', '[]', '[]', '::1', '', '2026-03-19 20:12:07.431743-03');
INSERT INTO public.tb_auditoria VALUES (75, 'excluir', 'workflow', 11, NULL, 1, 'admin', 'super_admin', '[]', '[]', '::1', '', '2026-03-19 20:12:09.639423-03');
INSERT INTO public.tb_auditoria VALUES (76, 'login', 'sessao', 1, 'admin', 1, 'admin', 'super_admin', '[]', '[]', '::1', '', '2026-03-19 20:28:19.105307-03');
INSERT INTO public.tb_auditoria VALUES (77, 'criar', 'usuario', 18, 'testuser_e2e', 1, 'admin', 'super_admin', '[]', '{"nivel_acesso": "operador"}', '::1', '', '2026-03-19 20:28:21.913445-03');
INSERT INTO public.tb_auditoria VALUES (78, 'login', 'sessao', 18, 'testuser_e2e', 18, 'testuser_e2e', 'operador', '[]', '[]', '::1', '', '2026-03-19 20:28:22.333803-03');
INSERT INTO public.tb_auditoria VALUES (79, 'logout', 'sessao', NULL, NULL, 18, 'testuser_e2e', 'operador', '[]', '[]', '::1', '', '2026-03-19 20:28:22.503382-03');
INSERT INTO public.tb_auditoria VALUES (80, 'logout', 'sessao', NULL, NULL, 0, 'sistema', '', '[]', '[]', '::1', '', '2026-03-19 20:28:22.9527-03');
INSERT INTO public.tb_auditoria VALUES (81, 'excluir', 'usuario', 18, 'testuser_e2e', 1, 'admin', 'super_admin', '[]', '[]', '::1', '', '2026-03-19 20:28:27.13243-03');
INSERT INTO public.tb_auditoria VALUES (82, 'login', 'sessao', 1, 'admin', 1, 'admin', 'super_admin', '[]', '[]', '::1', '', '2026-03-19 20:30:20.283338-03');
INSERT INTO public.tb_auditoria VALUES (83, 'criar', 'empresa', 2, 'Empresa Teste E2E', 1, 'admin', 'super_admin', '[]', '[]', '::1', '', '2026-03-19 20:30:22.210753-03');
INSERT INTO public.tb_auditoria VALUES (84, 'criar', 'usuario', 19, 'testuser_e2e', 1, 'admin', 'super_admin', '[]', '{"nivel_acesso": "operador"}', '::1', '', '2026-03-19 20:30:23.746877-03');
INSERT INTO public.tb_auditoria VALUES (85, 'login', 'sessao', 19, 'testuser_e2e', 19, 'testuser_e2e', 'operador', '[]', '[]', '::1', '', '2026-03-19 20:30:24.217479-03');
INSERT INTO public.tb_auditoria VALUES (86, 'logout', 'sessao', NULL, NULL, 19, 'testuser_e2e', 'operador', '[]', '[]', '::1', '', '2026-03-19 20:30:24.488693-03');
INSERT INTO public.tb_auditoria VALUES (87, 'login', 'sessao', 19, 'testuser_e2e', 19, 'testuser_e2e', 'operador', '[]', '[]', '::1', '', '2026-03-19 20:30:25.097745-03');
INSERT INTO public.tb_auditoria VALUES (88, 'logout', 'sessao', NULL, NULL, 19, 'testuser_e2e', 'operador', '[]', '[]', '::1', '', '2026-03-19 20:30:25.20853-03');
INSERT INTO public.tb_auditoria VALUES (89, 'criar', 'conexao', 0, '', 1, 'admin', 'super_admin', '[]', '[]', '::1', '', '2026-03-19 20:30:25.710545-03');
INSERT INTO public.tb_auditoria VALUES (90, 'excluir', 'usuario', 19, 'testuser_e2e', 1, 'admin', 'super_admin', '[]', '[]', '::1', '', '2026-03-19 20:30:30.258683-03');
INSERT INTO public.tb_auditoria VALUES (91, 'excluir', 'empresa', 2, 'Empresa Teste E2E', 1, 'admin', 'super_admin', '[]', '[]', '::1', '', '2026-03-19 20:30:30.653239-03');
INSERT INTO public.tb_auditoria VALUES (92, 'login', 'sessao', 1, 'admin', 1, 'admin', 'super_admin', '[]', '[]', '::1', '', '2026-03-19 20:31:26.836308-03');
INSERT INTO public.tb_auditoria VALUES (93, 'criar', 'empresa', 3, 'Empresa Teste E2E', 1, 'admin', 'super_admin', '[]', '[]', '::1', '', '2026-03-19 20:31:28.209447-03');
INSERT INTO public.tb_auditoria VALUES (94, 'criar', 'usuario', 20, 'testuser_e2e', 1, 'admin', 'super_admin', '[]', '{"nivel_acesso": "operador"}', '::1', '', '2026-03-19 20:31:28.799031-03');
INSERT INTO public.tb_auditoria VALUES (95, 'login', 'sessao', 20, 'testuser_e2e', 20, 'testuser_e2e', 'operador', '[]', '[]', '::1', '', '2026-03-19 20:31:29.211732-03');
INSERT INTO public.tb_auditoria VALUES (96, 'logout', 'sessao', NULL, NULL, 20, 'testuser_e2e', 'operador', '[]', '[]', '::1', '', '2026-03-19 20:31:29.417652-03');
INSERT INTO public.tb_auditoria VALUES (97, 'login', 'sessao', 20, 'testuser_e2e', 20, 'testuser_e2e', 'operador', '[]', '[]', '::1', '', '2026-03-19 20:31:29.796661-03');
INSERT INTO public.tb_auditoria VALUES (98, 'logout', 'sessao', NULL, NULL, 20, 'testuser_e2e', 'operador', '[]', '[]', '::1', '', '2026-03-19 20:31:29.903201-03');
INSERT INTO public.tb_auditoria VALUES (99, 'criar', 'conexao', 19, '', 1, 'admin', 'super_admin', '[]', '[]', '::1', '', '2026-03-19 20:31:30.496336-03');
INSERT INTO public.tb_auditoria VALUES (100, 'excluir', 'conexao', 19, NULL, 1, 'admin', 'super_admin', '[]', '[]', '::1', '', '2026-03-19 20:31:35.329932-03');
INSERT INTO public.tb_auditoria VALUES (101, 'excluir', 'usuario', 20, 'testuser_e2e', 1, 'admin', 'super_admin', '[]', '[]', '::1', '', '2026-03-19 20:31:35.481846-03');
INSERT INTO public.tb_auditoria VALUES (102, 'excluir', 'empresa', 3, 'Empresa Teste E2E', 1, 'admin', 'super_admin', '[]', '[]', '::1', '', '2026-03-19 20:31:35.791054-03');
INSERT INTO public.tb_auditoria VALUES (103, 'login', 'sessao', 1, 'admin', 1, 'admin', 'super_admin', '[]', '[]', '::1', '', '2026-03-19 20:31:44.196024-03');
INSERT INTO public.tb_auditoria VALUES (104, 'login', 'sessao', 1, 'admin', 1, 'admin', 'super_admin', '[]', '[]', '::1', '', '2026-03-19 20:31:47.737739-03');
INSERT INTO public.tb_auditoria VALUES (105, 'login', 'sessao', 21, 'test_dev', 21, 'test_dev', 'desenvolvedor', '[]', '[]', '::1', '', '2026-03-19 20:31:47.898853-03');
INSERT INTO public.tb_auditoria VALUES (106, 'login', 'sessao', 22, 'test_op', 22, 'test_op', 'operador', '[]', '[]', '::1', '', '2026-03-19 20:31:48.061584-03');
INSERT INTO public.tb_auditoria VALUES (107, 'login', 'sessao', 1, 'admin', 1, 'admin', 'super_admin', '[]', '[]', '::1', '', '2026-03-19 20:31:54.528349-03');
INSERT INTO public.tb_auditoria VALUES (108, 'editar', 'configuracao', NULL, 'Grupo: geral', 1, 'admin', 'super_admin', '{"app_nome": "DMC DataLoad"}', '{"app_nome": "DMC DataLoad"}', '::1', '', '2026-03-19 20:31:54.951148-03');
INSERT INTO public.tb_auditoria VALUES (109, 'login', 'sessao', 1, 'admin', 1, 'admin', 'super_admin', '[]', '[]', '::1', '', '2026-03-19 20:31:58.790257-03');
INSERT INTO public.tb_auditoria VALUES (110, 'criar', 'pipeline', 20, 'Pipeline Teste E2E', 1, 'admin', 'super_admin', '[]', '[]', '::1', '', '2026-03-19 20:31:59.176626-03');
INSERT INTO public.tb_auditoria VALUES (111, 'excluir', 'pipeline', 20, NULL, 1, 'admin', 'super_admin', '[]', '[]', '::1', '', '2026-03-19 20:31:59.459417-03');
INSERT INTO public.tb_auditoria VALUES (112, 'criar', 'workflow', 12, 'Workflow Teste E2E', 1, 'admin', 'super_admin', '[]', '[]', '::1', '', '2026-03-19 20:31:59.726329-03');
INSERT INTO public.tb_auditoria VALUES (113, 'excluir', 'workflow', 12, NULL, 1, 'admin', 'super_admin', '[]', '[]', '::1', '', '2026-03-19 20:32:00.086468-03');
INSERT INTO public.tb_auditoria VALUES (114, 'login', 'sessao', 1, 'admin', 1, 'admin', 'super_admin', '[]', '[]', '::1', '', '2026-03-19 20:32:03.753866-03');
INSERT INTO public.tb_auditoria VALUES (115, 'logout', 'sessao', NULL, NULL, 1, 'admin', 'super_admin', '[]', '[]', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-19 20:33:05.170013-03');
INSERT INTO public.tb_auditoria VALUES (116, 'login', 'sessao', 1, 'admin', 1, 'admin', 'super_admin', '[]', '[]', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-19 20:33:11.805089-03');
INSERT INTO public.tb_auditoria VALUES (117, 'logout', 'sessao', NULL, NULL, 1, 'admin', 'super_admin', '[]', '[]', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-19 20:38:49.381966-03');
INSERT INTO public.tb_auditoria VALUES (118, 'login', 'sessao', 1, 'admin', 1, 'admin', 'super_admin', '[]', '[]', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-19 20:39:10.263734-03');
INSERT INTO public.tb_auditoria VALUES (119, 'editar', 'usuario', 13, 'caio', 1, 'admin', 'super_admin', '[]', '{"nivel_acesso": "desenvolvedor"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-19 20:39:23.987879-03');
INSERT INTO public.tb_auditoria VALUES (120, 'logout', 'sessao', NULL, NULL, 1, 'admin', 'super_admin', '[]', '[]', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-19 20:39:27.086378-03');
INSERT INTO public.tb_auditoria VALUES (121, 'login', 'sessao', 13, 'caio', 13, 'caio', 'desenvolvedor', '[]', '[]', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-19 20:39:30.910861-03');
INSERT INTO public.tb_auditoria VALUES (122, 'logout', 'sessao', NULL, NULL, 13, 'caio', 'desenvolvedor', '[]', '[]', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-19 20:39:41.393379-03');
INSERT INTO public.tb_auditoria VALUES (123, 'login', 'sessao', 1, 'admin', 1, 'admin', 'super_admin', '[]', '[]', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-19 20:39:46.132131-03');
INSERT INTO public.tb_auditoria VALUES (124, 'criar', 'usuario', 23, 'renan', 1, 'admin', 'super_admin', '[]', '{"nivel_acesso": "operador"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-19 20:40:01.094794-03');
INSERT INTO public.tb_auditoria VALUES (125, 'logout', 'sessao', NULL, NULL, 1, 'admin', 'super_admin', '[]', '[]', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-19 20:40:04.197066-03');
INSERT INTO public.tb_auditoria VALUES (126, 'login', 'sessao', 23, 'renan', 23, 'renan', 'operador', '[]', '[]', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-19 20:40:10.062082-03');
INSERT INTO public.tb_auditoria VALUES (127, 'logout', 'sessao', NULL, NULL, 23, 'renan', 'operador', '[]', '[]', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-19 20:40:37.417614-03');
INSERT INTO public.tb_auditoria VALUES (128, 'login', 'sessao', 1, 'admin', 1, 'admin', 'super_admin', '[]', '[]', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-19 20:40:41.248279-03');
INSERT INTO public.tb_auditoria VALUES (129, 'editar', 'usuario', 23, 'renan', 1, 'admin', 'super_admin', '[]', '{"nivel_acesso": "operador"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-19 20:40:49.985556-03');
INSERT INTO public.tb_auditoria VALUES (130, 'logout', 'sessao', NULL, NULL, 1, 'admin', 'super_admin', '[]', '[]', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-19 20:40:52.815567-03');
INSERT INTO public.tb_auditoria VALUES (131, 'login', 'sessao', 23, 'renan', 23, 'renan', 'operador', '[]', '[]', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-19 20:41:00.457724-03');
INSERT INTO public.tb_auditoria VALUES (132, 'logout', 'sessao', NULL, NULL, 23, 'renan', 'operador', '[]', '[]', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-19 20:41:13.539047-03');
INSERT INTO public.tb_auditoria VALUES (133, 'login', 'sessao', 13, 'caio', 13, 'caio', 'desenvolvedor', '[]', '[]', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-19 20:41:17.470919-03');
INSERT INTO public.tb_auditoria VALUES (134, 'logout', 'sessao', NULL, NULL, 13, 'caio', 'desenvolvedor', '[]', '[]', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-19 20:41:27.972783-03');
INSERT INTO public.tb_auditoria VALUES (135, 'login', 'sessao', 1, 'admin', 1, 'admin', 'super_admin', '[]', '[]', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-19 20:41:31.729675-03');
INSERT INTO public.tb_auditoria VALUES (136, 'login', 'sessao', 1, 'admin', 1, 'admin', 'super_admin', '[]', '[]', '::1', '', '2026-03-19 20:46:25.198407-03');
INSERT INTO public.tb_auditoria VALUES (137, 'criar', 'empresa', 4, 'Empresa Teste E2E', 1, 'admin', 'super_admin', '[]', '[]', '::1', '', '2026-03-19 20:46:26.113428-03');
INSERT INTO public.tb_auditoria VALUES (138, 'criar', 'usuario', 24, 'testuser_e2e', 1, 'admin', 'super_admin', '[]', '{"nivel_acesso": "operador"}', '::1', '', '2026-03-19 20:46:26.542442-03');
INSERT INTO public.tb_auditoria VALUES (139, 'login', 'sessao', 24, 'testuser_e2e', 24, 'testuser_e2e', 'operador', '[]', '[]', '::1', '', '2026-03-19 20:46:26.97069-03');
INSERT INTO public.tb_auditoria VALUES (140, 'logout', 'sessao', NULL, NULL, 24, 'testuser_e2e', 'operador', '[]', '[]', '::1', '', '2026-03-19 20:46:27.163516-03');
INSERT INTO public.tb_auditoria VALUES (141, 'login', 'sessao', 24, 'testuser_e2e', 24, 'testuser_e2e', 'operador', '[]', '[]', '::1', '', '2026-03-19 20:46:27.571002-03');
INSERT INTO public.tb_auditoria VALUES (142, 'logout', 'sessao', NULL, NULL, 24, 'testuser_e2e', 'operador', '[]', '[]', '::1', '', '2026-03-19 20:46:27.672402-03');
INSERT INTO public.tb_auditoria VALUES (143, 'criar', 'conexao', 20, '', 1, 'admin', 'super_admin', '[]', '[]', '::1', '', '2026-03-19 20:46:28.208915-03');
INSERT INTO public.tb_auditoria VALUES (144, 'excluir', 'conexao', 20, NULL, 1, 'admin', 'super_admin', '[]', '[]', '::1', '', '2026-03-19 20:46:32.320422-03');
INSERT INTO public.tb_auditoria VALUES (145, 'excluir', 'usuario', 24, 'testuser_e2e', 1, 'admin', 'super_admin', '[]', '[]', '::1', '', '2026-03-19 20:46:32.47297-03');
INSERT INTO public.tb_auditoria VALUES (146, 'excluir', 'empresa', 4, 'Empresa Teste E2E', 1, 'admin', 'super_admin', '[]', '[]', '::1', '', '2026-03-19 20:46:32.775217-03');
INSERT INTO public.tb_auditoria VALUES (147, 'login', 'sessao', 1, 'admin', 1, 'admin', 'super_admin', '[]', '[]', '::1', '', '2026-03-19 20:51:38.952367-03');
INSERT INTO public.tb_auditoria VALUES (148, 'criar', 'empresa', 5, 'Empresa Teste E2E', 1, 'admin', 'super_admin', '[]', '[]', '::1', '', '2026-03-19 20:51:39.735986-03');
INSERT INTO public.tb_auditoria VALUES (149, 'criar', 'usuario', 25, 'testuser_e2e', 1, 'admin', 'super_admin', '[]', '{"nivel_acesso": "operador"}', '::1', '', '2026-03-19 20:51:40.329031-03');
INSERT INTO public.tb_auditoria VALUES (150, 'login', 'sessao', 25, 'testuser_e2e', 25, 'testuser_e2e', 'operador', '[]', '[]', '::1', '', '2026-03-19 20:51:40.691985-03');
INSERT INTO public.tb_auditoria VALUES (151, 'logout', 'sessao', NULL, NULL, 25, 'testuser_e2e', 'operador', '[]', '[]', '::1', '', '2026-03-19 20:51:40.84908-03');
INSERT INTO public.tb_auditoria VALUES (152, 'login', 'sessao', 25, 'testuser_e2e', 25, 'testuser_e2e', 'operador', '[]', '[]', '::1', '', '2026-03-19 20:51:41.210465-03');
INSERT INTO public.tb_auditoria VALUES (153, 'logout', 'sessao', NULL, NULL, 25, 'testuser_e2e', 'operador', '[]', '[]', '::1', '', '2026-03-19 20:51:41.303814-03');
INSERT INTO public.tb_auditoria VALUES (154, 'criar', 'conexao', 21, '', 1, 'admin', 'super_admin', '[]', '[]', '::1', '', '2026-03-19 20:51:41.950133-03');
INSERT INTO public.tb_auditoria VALUES (155, 'excluir', 'conexao', 21, NULL, 1, 'admin', 'super_admin', '[]', '[]', '::1', '', '2026-03-19 20:51:46.914781-03');
INSERT INTO public.tb_auditoria VALUES (156, 'excluir', 'usuario', 25, 'testuser_e2e', 1, 'admin', 'super_admin', '[]', '[]', '::1', '', '2026-03-19 20:51:47.073059-03');
INSERT INTO public.tb_auditoria VALUES (157, 'excluir', 'empresa', 5, 'Empresa Teste E2E', 1, 'admin', 'super_admin', '[]', '[]', '::1', '', '2026-03-19 20:51:47.342828-03');
INSERT INTO public.tb_auditoria VALUES (158, 'login', 'sessao', 1, 'admin', 1, 'admin', 'super_admin', '[]', '[]', '::1', '', '2026-03-19 20:52:00.810621-03');
INSERT INTO public.tb_auditoria VALUES (159, 'criar', 'empresa', 6, 'Empresa Teste E2E', 1, 'admin', 'super_admin', '[]', '[]', '::1', '', '2026-03-19 20:52:01.881594-03');
INSERT INTO public.tb_auditoria VALUES (160, 'criar', 'usuario', 26, 'testuser_e2e', 1, 'admin', 'super_admin', '[]', '{"nivel_acesso": "operador"}', '::1', '', '2026-03-19 20:52:02.438577-03');
INSERT INTO public.tb_auditoria VALUES (161, 'login', 'sessao', 26, 'testuser_e2e', 26, 'testuser_e2e', 'operador', '[]', '[]', '::1', '', '2026-03-19 20:52:02.830996-03');
INSERT INTO public.tb_auditoria VALUES (162, 'logout', 'sessao', NULL, NULL, 26, 'testuser_e2e', 'operador', '[]', '[]', '::1', '', '2026-03-19 20:52:03.01532-03');
INSERT INTO public.tb_auditoria VALUES (163, 'login', 'sessao', 26, 'testuser_e2e', 26, 'testuser_e2e', 'operador', '[]', '[]', '::1', '', '2026-03-19 20:52:03.402242-03');
INSERT INTO public.tb_auditoria VALUES (164, 'logout', 'sessao', NULL, NULL, 26, 'testuser_e2e', 'operador', '[]', '[]', '::1', '', '2026-03-19 20:52:03.495742-03');
INSERT INTO public.tb_auditoria VALUES (165, 'criar', 'conexao', 22, '', 1, 'admin', 'super_admin', '[]', '[]', '::1', '', '2026-03-19 20:52:04.044897-03');
INSERT INTO public.tb_auditoria VALUES (166, 'excluir', 'conexao', 22, NULL, 1, 'admin', 'super_admin', '[]', '[]', '::1', '', '2026-03-19 20:52:08.888611-03');
INSERT INTO public.tb_auditoria VALUES (167, 'excluir', 'usuario', 26, 'testuser_e2e', 1, 'admin', 'super_admin', '[]', '[]', '::1', '', '2026-03-19 20:52:09.041265-03');
INSERT INTO public.tb_auditoria VALUES (168, 'excluir', 'empresa', 6, 'Empresa Teste E2E', 1, 'admin', 'super_admin', '[]', '[]', '::1', '', '2026-03-19 20:52:09.301237-03');
INSERT INTO public.tb_auditoria VALUES (169, 'login', 'sessao', 1, 'admin', 1, 'admin', 'super_admin', '[]', '[]', '::1', '', '2026-03-19 20:52:15.267622-03');
INSERT INTO public.tb_auditoria VALUES (170, 'login', 'sessao', 1, 'admin', 1, 'admin', 'super_admin', '[]', '[]', '::1', '', '2026-03-19 20:52:22.6716-03');
INSERT INTO public.tb_auditoria VALUES (171, 'login', 'sessao', 27, 'test_dev', 27, 'test_dev', 'desenvolvedor', '[]', '[]', '::1', '', '2026-03-19 20:52:22.78841-03');
INSERT INTO public.tb_auditoria VALUES (172, 'login', 'sessao', 28, 'test_op', 28, 'test_op', 'operador', '[]', '[]', '::1', '', '2026-03-19 20:52:22.929021-03');
INSERT INTO public.tb_auditoria VALUES (173, 'login', 'sessao', 1, 'admin', 1, 'admin', 'super_admin', '[]', '[]', '::1', '', '2026-03-19 20:52:26.701613-03');
INSERT INTO public.tb_auditoria VALUES (174, 'editar', 'configuracao', NULL, 'Grupo: geral', 1, 'admin', 'super_admin', '{"app_nome": "DMC DataLoad"}', '{"app_nome": "DMC DataLoad"}', '::1', '', '2026-03-19 20:52:27.13398-03');
INSERT INTO public.tb_auditoria VALUES (175, 'login', 'sessao', 1, 'admin', 1, 'admin', 'super_admin', '[]', '[]', '::1', '', '2026-03-19 20:52:29.390677-03');
INSERT INTO public.tb_auditoria VALUES (176, 'criar', 'pipeline', 21, 'Pipeline Teste E2E', 1, 'admin', 'super_admin', '[]', '[]', '::1', '', '2026-03-19 20:52:29.632755-03');
INSERT INTO public.tb_auditoria VALUES (177, 'excluir', 'pipeline', 21, NULL, 1, 'admin', 'super_admin', '[]', '[]', '::1', '', '2026-03-19 20:52:29.870531-03');
INSERT INTO public.tb_auditoria VALUES (178, 'criar', 'workflow', 13, 'Workflow Teste E2E', 1, 'admin', 'super_admin', '[]', '[]', '::1', '', '2026-03-19 20:52:30.128482-03');
INSERT INTO public.tb_auditoria VALUES (179, 'excluir', 'workflow', 13, NULL, 1, 'admin', 'super_admin', '[]', '[]', '::1', '', '2026-03-19 20:52:30.463762-03');
INSERT INTO public.tb_auditoria VALUES (180, 'login', 'sessao', 1, 'admin', 1, 'admin', 'super_admin', '[]', '[]', '::1', '', '2026-03-19 20:52:32.111619-03');
INSERT INTO public.tb_auditoria VALUES (181, 'login', 'sessao', 1, 'admin', 1, 'admin', 'super_admin', '[]', '[]', '::1', 'DMC-BrowserTest/2.0 (Simulated Browser)', '2026-03-20 10:14:00.51217-03');
INSERT INTO public.tb_auditoria VALUES (182, 'criar', 'conexao', 23, '', 1, 'admin', 'super_admin', '[]', '[]', '::1', 'DMC-BrowserTest/2.0 (Simulated Browser)', '2026-03-20 10:14:12.168891-03');
INSERT INTO public.tb_auditoria VALUES (396, 'login', 'sessao', 1, 'admin', 1, 'admin', 'super_admin', '[]', '[]', '::1', '', '2026-03-20 11:09:15.055395-03');
INSERT INTO public.tb_auditoria VALUES (183, 'excluir', 'conexao', 23, NULL, 1, 'admin', 'super_admin', '[]', '[]', '::1', 'DMC-BrowserTest/2.0 (Simulated Browser)', '2026-03-20 10:14:22.28838-03');
INSERT INTO public.tb_auditoria VALUES (184, 'login', 'sessao', 1, 'admin', 1, 'admin', 'super_admin', '[]', '[]', '::1', '', '2026-03-20 10:18:49.956673-03');
INSERT INTO public.tb_auditoria VALUES (185, 'login', 'sessao', 1, 'admin', 1, 'admin', 'super_admin', '[]', '[]', '::1', 'DMC-BrowserTest/2.0 (Simulated Browser)', '2026-03-20 10:22:10.376061-03');
INSERT INTO public.tb_auditoria VALUES (186, 'login', 'sessao', 1, 'admin', 1, 'admin', 'super_admin', '[]', '[]', '::1', '', '2026-03-20 10:23:12.523356-03');
INSERT INTO public.tb_auditoria VALUES (187, 'login', 'sessao', 1, 'admin', 1, 'admin', 'super_admin', '[]', '[]', '::1', 'DMC-BrowserTest/2.0 (Simulated Browser)', '2026-03-20 10:23:23.867607-03');
INSERT INTO public.tb_auditoria VALUES (188, 'criar', 'empresa', 7, 'DMC Tecnologia', 1, 'admin', 'super_admin', '[]', '[]', '::1', 'DMC-BrowserTest/2.0 (Simulated Browser)', '2026-03-20 10:23:24.040499-03');
INSERT INTO public.tb_auditoria VALUES (189, 'criar', 'empresa', 8, 'Alpha Corp', 1, 'admin', 'super_admin', '[]', '[]', '::1', 'DMC-BrowserTest/2.0 (Simulated Browser)', '2026-03-20 10:23:24.192486-03');
INSERT INTO public.tb_auditoria VALUES (190, 'criar', 'empresa', 9, 'Beta Sistemas', 1, 'admin', 'super_admin', '[]', '[]', '::1', 'DMC-BrowserTest/2.0 (Simulated Browser)', '2026-03-20 10:23:24.346089-03');
INSERT INTO public.tb_auditoria VALUES (191, 'criar', 'usuario', 29, 'admin_browser_test', 1, 'admin', 'super_admin', '[]', '{"nivel_acesso": "admin"}', '::1', 'DMC-BrowserTest/2.0 (Simulated Browser)', '2026-03-20 10:23:25.396063-03');
INSERT INTO public.tb_auditoria VALUES (192, 'criar', 'usuario', 30, 'dev_browser_test', 1, 'admin', 'super_admin', '[]', '{"nivel_acesso": "desenvolvedor"}', '::1', 'DMC-BrowserTest/2.0 (Simulated Browser)', '2026-03-20 10:23:25.628357-03');
INSERT INTO public.tb_auditoria VALUES (193, 'criar', 'usuario', 31, 'op_browser_test', 1, 'admin', 'super_admin', '[]', '{"nivel_acesso": "operador"}', '::1', 'DMC-BrowserTest/2.0 (Simulated Browser)', '2026-03-20 10:23:25.834825-03');
INSERT INTO public.tb_auditoria VALUES (194, 'criar', 'conexao', 24, '', 1, 'admin', 'super_admin', '[]', '[]', '::1', 'DMC-BrowserTest/2.0 (Simulated Browser)', '2026-03-20 10:23:32.097011-03');
INSERT INTO public.tb_auditoria VALUES (195, 'login', 'sessao', 29, 'admin_browser_test', 29, 'admin_browser_test', 'admin', '[]', '[]', '::1', 'DMC-BrowserTest/2.0 (Simulated Browser)', '2026-03-20 10:23:32.946903-03');
INSERT INTO public.tb_auditoria VALUES (196, 'criar', 'conexao', 25, '', 29, 'admin_browser_test', 'admin', '[]', '[]', '::1', 'DMC-BrowserTest/2.0 (Simulated Browser)', '2026-03-20 10:23:35.456042-03');
INSERT INTO public.tb_auditoria VALUES (197, 'login', 'sessao', 30, 'dev_browser_test', 30, 'dev_browser_test', 'desenvolvedor', '[]', '[]', '::1', 'DMC-BrowserTest/2.0 (Simulated Browser)', '2026-03-20 10:23:35.898192-03');
INSERT INTO public.tb_auditoria VALUES (198, 'criar', 'conexao', 26, '', 30, 'dev_browser_test', 'desenvolvedor', '[]', '[]', '::1', 'DMC-BrowserTest/2.0 (Simulated Browser)', '2026-03-20 10:23:37.662204-03');
INSERT INTO public.tb_auditoria VALUES (199, 'login', 'sessao', 31, 'op_browser_test', 31, 'op_browser_test', 'operador', '[]', '[]', '::1', 'DMC-BrowserTest/2.0 (Simulated Browser)', '2026-03-20 10:23:38.24928-03');
INSERT INTO public.tb_auditoria VALUES (200, 'excluir', 'conexao', 24, NULL, 1, 'admin', 'super_admin', '[]', '[]', '::1', 'DMC-BrowserTest/2.0 (Simulated Browser)', '2026-03-20 10:23:42.996147-03');
INSERT INTO public.tb_auditoria VALUES (201, 'excluir', 'conexao', 25, NULL, 1, 'admin', 'super_admin', '[]', '[]', '::1', 'DMC-BrowserTest/2.0 (Simulated Browser)', '2026-03-20 10:23:43.539843-03');
INSERT INTO public.tb_auditoria VALUES (202, 'excluir', 'conexao', 26, NULL, 1, 'admin', 'super_admin', '[]', '[]', '::1', 'DMC-BrowserTest/2.0 (Simulated Browser)', '2026-03-20 10:23:43.997911-03');
INSERT INTO public.tb_auditoria VALUES (203, 'excluir', 'usuario', 29, 'admin_browser_test', 1, 'admin', 'super_admin', '[]', '[]', '::1', 'DMC-BrowserTest/2.0 (Simulated Browser)', '2026-03-20 10:23:44.155851-03');
INSERT INTO public.tb_auditoria VALUES (204, 'excluir', 'usuario', 30, 'dev_browser_test', 1, 'admin', 'super_admin', '[]', '[]', '::1', 'DMC-BrowserTest/2.0 (Simulated Browser)', '2026-03-20 10:23:44.313005-03');
INSERT INTO public.tb_auditoria VALUES (205, 'excluir', 'usuario', 31, 'op_browser_test', 1, 'admin', 'super_admin', '[]', '[]', '::1', 'DMC-BrowserTest/2.0 (Simulated Browser)', '2026-03-20 10:23:44.465527-03');
INSERT INTO public.tb_auditoria VALUES (206, 'excluir', 'empresa', 7, 'DMC Tecnologia', 1, 'admin', 'super_admin', '[]', '[]', '::1', 'DMC-BrowserTest/2.0 (Simulated Browser)', '2026-03-20 10:23:45.607256-03');
INSERT INTO public.tb_auditoria VALUES (207, 'excluir', 'empresa', 8, 'Alpha Corp', 1, 'admin', 'super_admin', '[]', '[]', '::1', 'DMC-BrowserTest/2.0 (Simulated Browser)', '2026-03-20 10:23:45.80325-03');
INSERT INTO public.tb_auditoria VALUES (208, 'excluir', 'empresa', 9, 'Beta Sistemas', 1, 'admin', 'super_admin', '[]', '[]', '::1', 'DMC-BrowserTest/2.0 (Simulated Browser)', '2026-03-20 10:23:46.010312-03');
INSERT INTO public.tb_auditoria VALUES (209, 'login', 'sessao', 1, 'admin', 1, 'admin', 'super_admin', '[]', '[]', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-20 10:24:23.190502-03');
INSERT INTO public.tb_auditoria VALUES (210, 'login', 'sessao', 23, 'renan', 23, 'renan', 'operador', '[]', '[]', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-20 10:24:48.66268-03');
INSERT INTO public.tb_auditoria VALUES (211, 'criar', 'usuario', 32, 'lucas', 1, 'admin', 'super_admin', '[]', '{"nivel_acesso": "admin"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-20 10:28:09.842111-03');
INSERT INTO public.tb_auditoria VALUES (212, 'logout', 'sessao', NULL, NULL, 23, 'renan', 'operador', '[]', '[]', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-20 10:28:17.309842-03');
INSERT INTO public.tb_auditoria VALUES (213, 'login', 'sessao', 32, 'lucas', 32, 'lucas', 'admin', '[]', '[]', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-20 10:28:28.394286-03');
INSERT INTO public.tb_auditoria VALUES (214, 'login', 'sessao', 1, 'admin', 1, 'admin', 'super_admin', '[]', '[]', '::1', 'DMC-BrowserTest/2.0 (Simulated Browser)', '2026-03-20 10:29:49.075996-03');
INSERT INTO public.tb_auditoria VALUES (215, 'criar', 'empresa', 10, 'DMC Tecnologia', 1, 'admin', 'super_admin', '[]', '[]', '::1', 'DMC-BrowserTest/2.0 (Simulated Browser)', '2026-03-20 10:29:49.210659-03');
INSERT INTO public.tb_auditoria VALUES (216, 'criar', 'empresa', 11, 'Alpha Corp', 1, 'admin', 'super_admin', '[]', '[]', '::1', 'DMC-BrowserTest/2.0 (Simulated Browser)', '2026-03-20 10:29:49.295997-03');
INSERT INTO public.tb_auditoria VALUES (217, 'criar', 'empresa', 12, 'Beta Sistemas', 1, 'admin', 'super_admin', '[]', '[]', '::1', 'DMC-BrowserTest/2.0 (Simulated Browser)', '2026-03-20 10:29:49.403319-03');
INSERT INTO public.tb_auditoria VALUES (218, 'criar', 'usuario', 33, 'admin_browser_test', 1, 'admin', 'super_admin', '[]', '{"nivel_acesso": "admin"}', '::1', 'DMC-BrowserTest/2.0 (Simulated Browser)', '2026-03-20 10:29:50.309859-03');
INSERT INTO public.tb_auditoria VALUES (219, 'criar', 'usuario', 34, 'dev_browser_test', 1, 'admin', 'super_admin', '[]', '{"nivel_acesso": "desenvolvedor"}', '::1', 'DMC-BrowserTest/2.0 (Simulated Browser)', '2026-03-20 10:29:50.524121-03');
INSERT INTO public.tb_auditoria VALUES (220, 'criar', 'usuario', 35, 'op_browser_test', 1, 'admin', 'super_admin', '[]', '{"nivel_acesso": "operador"}', '::1', 'DMC-BrowserTest/2.0 (Simulated Browser)', '2026-03-20 10:29:50.711504-03');
INSERT INTO public.tb_auditoria VALUES (221, 'criar', 'conexao', 27, '', 1, 'admin', 'super_admin', '[]', '[]', '::1', 'DMC-BrowserTest/2.0 (Simulated Browser)', '2026-03-20 10:29:56.097709-03');
INSERT INTO public.tb_auditoria VALUES (222, 'login', 'sessao', 33, 'admin_browser_test', 33, 'admin_browser_test', 'admin', '[]', '[]', '::1', 'DMC-BrowserTest/2.0 (Simulated Browser)', '2026-03-20 10:29:56.8952-03');
INSERT INTO public.tb_auditoria VALUES (223, 'criar', 'conexao', 28, '', 33, 'admin_browser_test', 'admin', '[]', '[]', '::1', 'DMC-BrowserTest/2.0 (Simulated Browser)', '2026-03-20 10:29:58.679887-03');
INSERT INTO public.tb_auditoria VALUES (224, 'login', 'sessao', 34, 'dev_browser_test', 34, 'dev_browser_test', 'desenvolvedor', '[]', '[]', '::1', 'DMC-BrowserTest/2.0 (Simulated Browser)', '2026-03-20 10:29:59.06425-03');
INSERT INTO public.tb_auditoria VALUES (225, 'criar', 'conexao', 29, '', 34, 'dev_browser_test', 'desenvolvedor', '[]', '[]', '::1', 'DMC-BrowserTest/2.0 (Simulated Browser)', '2026-03-20 10:30:00.473136-03');
INSERT INTO public.tb_auditoria VALUES (226, 'login', 'sessao', 35, 'op_browser_test', 35, 'op_browser_test', 'operador', '[]', '[]', '::1', 'DMC-BrowserTest/2.0 (Simulated Browser)', '2026-03-20 10:30:00.961261-03');
INSERT INTO public.tb_auditoria VALUES (227, 'excluir', 'conexao', 27, NULL, 1, 'admin', 'super_admin', '[]', '[]', '::1', 'DMC-BrowserTest/2.0 (Simulated Browser)', '2026-03-20 10:30:05.234367-03');
INSERT INTO public.tb_auditoria VALUES (228, 'excluir', 'conexao', 28, NULL, 1, 'admin', 'super_admin', '[]', '[]', '::1', 'DMC-BrowserTest/2.0 (Simulated Browser)', '2026-03-20 10:30:05.722598-03');
INSERT INTO public.tb_auditoria VALUES (229, 'excluir', 'conexao', 29, NULL, 1, 'admin', 'super_admin', '[]', '[]', '::1', 'DMC-BrowserTest/2.0 (Simulated Browser)', '2026-03-20 10:30:06.160918-03');
INSERT INTO public.tb_auditoria VALUES (230, 'excluir', 'usuario', 33, 'admin_browser_test', 1, 'admin', 'super_admin', '[]', '[]', '::1', 'DMC-BrowserTest/2.0 (Simulated Browser)', '2026-03-20 10:30:06.295119-03');
INSERT INTO public.tb_auditoria VALUES (231, 'excluir', 'usuario', 34, 'dev_browser_test', 1, 'admin', 'super_admin', '[]', '[]', '::1', 'DMC-BrowserTest/2.0 (Simulated Browser)', '2026-03-20 10:30:06.468399-03');
INSERT INTO public.tb_auditoria VALUES (232, 'excluir', 'usuario', 35, 'op_browser_test', 1, 'admin', 'super_admin', '[]', '[]', '::1', 'DMC-BrowserTest/2.0 (Simulated Browser)', '2026-03-20 10:30:06.610692-03');
INSERT INTO public.tb_auditoria VALUES (233, 'excluir', 'empresa', 10, 'DMC Tecnologia', 1, 'admin', 'super_admin', '[]', '[]', '::1', 'DMC-BrowserTest/2.0 (Simulated Browser)', '2026-03-20 10:30:07.534703-03');
INSERT INTO public.tb_auditoria VALUES (234, 'excluir', 'empresa', 11, 'Alpha Corp', 1, 'admin', 'super_admin', '[]', '[]', '::1', 'DMC-BrowserTest/2.0 (Simulated Browser)', '2026-03-20 10:30:07.670923-03');
INSERT INTO public.tb_auditoria VALUES (235, 'excluir', 'empresa', 12, 'Beta Sistemas', 1, 'admin', 'super_admin', '[]', '[]', '::1', 'DMC-BrowserTest/2.0 (Simulated Browser)', '2026-03-20 10:30:07.803208-03');
INSERT INTO public.tb_auditoria VALUES (236, 'logout', 'sessao', NULL, NULL, 32, 'lucas', 'admin', '[]', '[]', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-20 10:30:24.649864-03');
INSERT INTO public.tb_auditoria VALUES (237, 'login', 'sessao', 23, 'renan', 23, 'renan', 'operador', '[]', '[]', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-20 10:30:28.017404-03');
INSERT INTO public.tb_auditoria VALUES (238, 'login', 'sessao', 1, 'admin', 1, 'admin', 'super_admin', '[]', '[]', '::1', '', '2026-03-20 10:30:31.45486-03');
INSERT INTO public.tb_auditoria VALUES (239, 'criar', 'empresa', 13, 'Empresa Teste E2E', 1, 'admin', 'super_admin', '[]', '[]', '::1', '', '2026-03-20 10:30:33.78621-03');
INSERT INTO public.tb_auditoria VALUES (240, 'criar', 'usuario', 36, 'testuser_e2e', 1, 'admin', 'super_admin', '[]', '{"nivel_acesso": "operador"}', '::1', '', '2026-03-20 10:30:34.537848-03');
INSERT INTO public.tb_auditoria VALUES (241, 'login', 'sessao', 36, 'testuser_e2e', 36, 'testuser_e2e', 'operador', '[]', '[]', '::1', '', '2026-03-20 10:30:35.224485-03');
INSERT INTO public.tb_auditoria VALUES (242, 'logout', 'sessao', NULL, NULL, 36, 'testuser_e2e', 'operador', '[]', '[]', '::1', '', '2026-03-20 10:30:35.463469-03');
INSERT INTO public.tb_auditoria VALUES (243, 'login', 'sessao', 36, 'testuser_e2e', 36, 'testuser_e2e', 'operador', '[]', '[]', '::1', '', '2026-03-20 10:30:35.914258-03');
INSERT INTO public.tb_auditoria VALUES (244, 'logout', 'sessao', NULL, NULL, 36, 'testuser_e2e', 'operador', '[]', '[]', '::1', '', '2026-03-20 10:30:36.03966-03');
INSERT INTO public.tb_auditoria VALUES (245, 'criar', 'conexao', 30, '', 1, 'admin', 'super_admin', '[]', '[]', '::1', '', '2026-03-20 10:30:36.916476-03');
INSERT INTO public.tb_auditoria VALUES (246, 'excluir', 'conexao', 30, NULL, 1, 'admin', 'super_admin', '[]', '[]', '::1', '', '2026-03-20 10:30:41.78308-03');
INSERT INTO public.tb_auditoria VALUES (247, 'excluir', 'usuario', 36, 'testuser_e2e', 1, 'admin', 'super_admin', '[]', '[]', '::1', '', '2026-03-20 10:30:41.915436-03');
INSERT INTO public.tb_auditoria VALUES (248, 'excluir', 'empresa', 13, 'Empresa Teste E2E', 1, 'admin', 'super_admin', '[]', '[]', '::1', '', '2026-03-20 10:30:42.204582-03');
INSERT INTO public.tb_auditoria VALUES (249, 'login', 'sessao', 1, 'admin', 1, 'admin', 'super_admin', '[]', '[]', '::1', '', '2026-03-20 10:30:47.081452-03');
INSERT INTO public.tb_auditoria VALUES (250, 'login', 'sessao', 1, 'admin', 1, 'admin', 'super_admin', '[]', '[]', '::1', '', '2026-03-20 10:31:19.951105-03');
INSERT INTO public.tb_auditoria VALUES (251, 'login', 'sessao', 1, 'admin', 1, 'admin', 'super_admin', '[]', '[]', '::1', '', '2026-03-20 10:31:27.976169-03');
INSERT INTO public.tb_auditoria VALUES (252, 'editar', 'configuracao', NULL, 'Grupo: geral', 1, 'admin', 'super_admin', '{"app_nome": "DMC DataLoad"}', '{"app_nome": "DMC DataLoad"}', '::1', '', '2026-03-20 10:31:28.437055-03');
INSERT INTO public.tb_auditoria VALUES (253, 'login', 'sessao', 1, 'admin', 1, 'admin', 'super_admin', '[]', '[]', '::1', '', '2026-03-20 10:31:34.732148-03');
INSERT INTO public.tb_auditoria VALUES (254, 'login', 'sessao', 37, 'test_dev', 37, 'test_dev', 'desenvolvedor', '[]', '[]', '::1', '', '2026-03-20 10:31:34.847029-03');
INSERT INTO public.tb_auditoria VALUES (255, 'login', 'sessao', 38, 'test_op', 38, 'test_op', 'operador', '[]', '[]', '::1', '', '2026-03-20 10:31:34.984409-03');
INSERT INTO public.tb_auditoria VALUES (256, 'login', 'sessao', 1, 'admin', 1, 'admin', 'super_admin', '[]', '[]', '::1', '', '2026-03-20 10:32:30.912618-03');
INSERT INTO public.tb_auditoria VALUES (257, 'criar', 'pipeline', 22, 'Pipeline Teste E2E', 1, 'admin', 'super_admin', '[]', '[]', '::1', '', '2026-03-20 10:32:31.144101-03');
INSERT INTO public.tb_auditoria VALUES (258, 'excluir', 'pipeline', 22, NULL, 1, 'admin', 'super_admin', '[]', '[]', '::1', '', '2026-03-20 10:32:31.366255-03');
INSERT INTO public.tb_auditoria VALUES (259, 'criar', 'workflow', 14, 'Workflow Teste E2E', 1, 'admin', 'super_admin', '[]', '[]', '::1', '', '2026-03-20 10:32:31.600195-03');
INSERT INTO public.tb_auditoria VALUES (260, 'excluir', 'workflow', 14, NULL, 1, 'admin', 'super_admin', '[]', '[]', '::1', '', '2026-03-20 10:32:31.940502-03');
INSERT INTO public.tb_auditoria VALUES (261, 'logout', 'sessao', NULL, NULL, 23, 'renan', 'operador', '[]', '[]', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-20 10:33:00.44574-03');
INSERT INTO public.tb_auditoria VALUES (262, 'login', 'sessao', 23, 'renan', 23, 'renan', 'operador', '[]', '[]', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-20 10:33:03.969104-03');
INSERT INTO public.tb_auditoria VALUES (263, 'logout', 'sessao', NULL, NULL, 23, 'renan', 'operador', '[]', '[]', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-20 10:38:17.634759-03');
INSERT INTO public.tb_auditoria VALUES (264, 'login', 'sessao', 13, 'caio', 13, 'caio', 'desenvolvedor', '[]', '[]', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-20 10:38:22.81851-03');
INSERT INTO public.tb_auditoria VALUES (265, 'editar', 'conexao', 15, '', 1, 'admin', 'super_admin', '[]', '[]', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-20 10:39:01.818054-03');
INSERT INTO public.tb_auditoria VALUES (266, 'editar', 'conexao', 15, '', 1, 'admin', 'super_admin', '[]', '[]', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-20 10:46:50.011432-03');
INSERT INTO public.tb_auditoria VALUES (267, 'editar', 'conexao', 15, '', 1, 'admin', 'super_admin', '[]', '[]', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-20 10:46:52.596057-03');
INSERT INTO public.tb_auditoria VALUES (268, 'editar', 'conexao', 11, '', 1, 'admin', 'super_admin', '[]', '[]', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-20 10:47:39.989723-03');
INSERT INTO public.tb_auditoria VALUES (269, 'logout', 'sessao', NULL, NULL, 13, 'caio', 'desenvolvedor', '[]', '[]', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-20 10:47:51.591527-03');
INSERT INTO public.tb_auditoria VALUES (270, 'login', 'sessao', 23, 'renan', 23, 'renan', 'operador', '[]', '[]', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-20 10:47:54.581772-03');
INSERT INTO public.tb_auditoria VALUES (271, 'logout', 'sessao', NULL, NULL, 23, 'renan', 'operador', '[]', '[]', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-20 10:48:07.780896-03');
INSERT INTO public.tb_auditoria VALUES (272, 'login', 'sessao', 23, 'renan', 23, 'renan', 'operador', '[]', '[]', '::1', '', '2026-03-20 10:49:07.054918-03');
INSERT INTO public.tb_auditoria VALUES (273, 'editar', 'conexao', 15, '', 1, 'admin', 'super_admin', '[]', '[]', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-20 10:49:34.694848-03');
INSERT INTO public.tb_auditoria VALUES (274, 'login', 'sessao', 23, 'renan', 23, 'renan', 'operador', '[]', '[]', '::1', '', '2026-03-20 10:51:23.920876-03');
INSERT INTO public.tb_auditoria VALUES (275, 'login', 'sessao', 23, 'renan', 23, 'renan', 'operador', '[]', '[]', '::1', '', '2026-03-20 10:52:02.636394-03');
INSERT INTO public.tb_auditoria VALUES (276, 'login', 'sessao', 23, 'renan', 23, 'renan', 'operador', '[]', '[]', '::1', '', '2026-03-20 10:52:18.188657-03');
INSERT INTO public.tb_auditoria VALUES (277, 'login', 'sessao', 23, 'renan', 23, 'renan', 'operador', '[]', '[]', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-20 10:52:31.225248-03');
INSERT INTO public.tb_auditoria VALUES (278, 'login', 'sessao', 1, 'admin', 1, 'admin', 'super_admin', '[]', '[]', '::1', 'DMC-BrowserTest/2.0 (Simulated Browser)', '2026-03-20 10:52:51.755221-03');
INSERT INTO public.tb_auditoria VALUES (279, 'criar', 'empresa', 14, 'DMC Tecnologia', 1, 'admin', 'super_admin', '[]', '[]', '::1', 'DMC-BrowserTest/2.0 (Simulated Browser)', '2026-03-20 10:52:51.901453-03');
INSERT INTO public.tb_auditoria VALUES (280, 'criar', 'empresa', 15, 'Alpha Corp', 1, 'admin', 'super_admin', '[]', '[]', '::1', 'DMC-BrowserTest/2.0 (Simulated Browser)', '2026-03-20 10:52:52.038408-03');
INSERT INTO public.tb_auditoria VALUES (281, 'criar', 'empresa', 16, 'Beta Sistemas', 1, 'admin', 'super_admin', '[]', '[]', '::1', 'DMC-BrowserTest/2.0 (Simulated Browser)', '2026-03-20 10:52:52.222308-03');
INSERT INTO public.tb_auditoria VALUES (282, 'criar', 'usuario', 39, 'admin_browser_test', 1, 'admin', 'super_admin', '[]', '{"nivel_acesso": "admin"}', '::1', 'DMC-BrowserTest/2.0 (Simulated Browser)', '2026-03-20 10:52:53.382409-03');
INSERT INTO public.tb_auditoria VALUES (283, 'criar', 'usuario', 40, 'dev_browser_test', 1, 'admin', 'super_admin', '[]', '{"nivel_acesso": "desenvolvedor"}', '::1', 'DMC-BrowserTest/2.0 (Simulated Browser)', '2026-03-20 10:52:53.617299-03');
INSERT INTO public.tb_auditoria VALUES (284, 'criar', 'usuario', 41, 'op_browser_test', 1, 'admin', 'super_admin', '[]', '{"nivel_acesso": "operador"}', '::1', 'DMC-BrowserTest/2.0 (Simulated Browser)', '2026-03-20 10:52:53.856308-03');
INSERT INTO public.tb_auditoria VALUES (285, 'criar', 'conexao', 31, '', 1, 'admin', 'super_admin', '[]', '[]', '::1', 'DMC-BrowserTest/2.0 (Simulated Browser)', '2026-03-20 10:52:59.049101-03');
INSERT INTO public.tb_auditoria VALUES (286, 'login', 'sessao', 39, 'admin_browser_test', 39, 'admin_browser_test', 'admin', '[]', '[]', '::1', 'DMC-BrowserTest/2.0 (Simulated Browser)', '2026-03-20 10:52:59.968602-03');
INSERT INTO public.tb_auditoria VALUES (287, 'criar', 'conexao', 32, '', 39, 'admin_browser_test', 'admin', '[]', '[]', '::1', 'DMC-BrowserTest/2.0 (Simulated Browser)', '2026-03-20 10:53:01.75638-03');
INSERT INTO public.tb_auditoria VALUES (288, 'login', 'sessao', 40, 'dev_browser_test', 40, 'dev_browser_test', 'desenvolvedor', '[]', '[]', '::1', 'DMC-BrowserTest/2.0 (Simulated Browser)', '2026-03-20 10:53:02.149643-03');
INSERT INTO public.tb_auditoria VALUES (289, 'criar', 'conexao', 33, '', 40, 'dev_browser_test', 'desenvolvedor', '[]', '[]', '::1', 'DMC-BrowserTest/2.0 (Simulated Browser)', '2026-03-20 10:53:03.592793-03');
INSERT INTO public.tb_auditoria VALUES (290, 'login', 'sessao', 41, 'op_browser_test', 41, 'op_browser_test', 'operador', '[]', '[]', '::1', 'DMC-BrowserTest/2.0 (Simulated Browser)', '2026-03-20 10:53:04.092615-03');
INSERT INTO public.tb_auditoria VALUES (291, 'excluir', 'conexao', 31, NULL, 1, 'admin', 'super_admin', '[]', '[]', '::1', 'DMC-BrowserTest/2.0 (Simulated Browser)', '2026-03-20 10:53:09.942424-03');
INSERT INTO public.tb_auditoria VALUES (292, 'excluir', 'conexao', 32, NULL, 1, 'admin', 'super_admin', '[]', '[]', '::1', 'DMC-BrowserTest/2.0 (Simulated Browser)', '2026-03-20 10:53:10.597705-03');
INSERT INTO public.tb_auditoria VALUES (293, 'excluir', 'conexao', 33, NULL, 1, 'admin', 'super_admin', '[]', '[]', '::1', 'DMC-BrowserTest/2.0 (Simulated Browser)', '2026-03-20 10:53:11.099412-03');
INSERT INTO public.tb_auditoria VALUES (294, 'excluir', 'usuario', 39, 'admin_browser_test', 1, 'admin', 'super_admin', '[]', '[]', '::1', 'DMC-BrowserTest/2.0 (Simulated Browser)', '2026-03-20 10:53:11.273514-03');
INSERT INTO public.tb_auditoria VALUES (295, 'excluir', 'usuario', 40, 'dev_browser_test', 1, 'admin', 'super_admin', '[]', '[]', '::1', 'DMC-BrowserTest/2.0 (Simulated Browser)', '2026-03-20 10:53:11.43322-03');
INSERT INTO public.tb_auditoria VALUES (296, 'excluir', 'usuario', 41, 'op_browser_test', 1, 'admin', 'super_admin', '[]', '[]', '::1', 'DMC-BrowserTest/2.0 (Simulated Browser)', '2026-03-20 10:53:11.598327-03');
INSERT INTO public.tb_auditoria VALUES (297, 'excluir', 'empresa', 14, 'DMC Tecnologia', 1, 'admin', 'super_admin', '[]', '[]', '::1', 'DMC-BrowserTest/2.0 (Simulated Browser)', '2026-03-20 10:53:12.68852-03');
INSERT INTO public.tb_auditoria VALUES (298, 'excluir', 'empresa', 15, 'Alpha Corp', 1, 'admin', 'super_admin', '[]', '[]', '::1', 'DMC-BrowserTest/2.0 (Simulated Browser)', '2026-03-20 10:53:12.850319-03');
INSERT INTO public.tb_auditoria VALUES (299, 'excluir', 'empresa', 16, 'Beta Sistemas', 1, 'admin', 'super_admin', '[]', '[]', '::1', 'DMC-BrowserTest/2.0 (Simulated Browser)', '2026-03-20 10:53:13.031586-03');
INSERT INTO public.tb_auditoria VALUES (300, 'login', 'sessao', 1, 'admin', 1, 'admin', 'super_admin', '[]', '[]', '::1', '', '2026-03-20 10:53:26.415913-03');
INSERT INTO public.tb_auditoria VALUES (301, 'criar', 'empresa', 17, 'Empresa Teste E2E', 1, 'admin', 'super_admin', '[]', '[]', '::1', '', '2026-03-20 10:53:27.656602-03');
INSERT INTO public.tb_auditoria VALUES (302, 'criar', 'usuario', 42, 'testuser_e2e', 1, 'admin', 'super_admin', '[]', '{"nivel_acesso": "operador"}', '::1', '', '2026-03-20 10:53:28.235762-03');
INSERT INTO public.tb_auditoria VALUES (303, 'login', 'sessao', 42, 'testuser_e2e', 42, 'testuser_e2e', 'operador', '[]', '[]', '::1', '', '2026-03-20 10:53:28.637075-03');
INSERT INTO public.tb_auditoria VALUES (304, 'logout', 'sessao', NULL, NULL, 42, 'testuser_e2e', 'operador', '[]', '[]', '::1', '', '2026-03-20 10:53:28.818775-03');
INSERT INTO public.tb_auditoria VALUES (305, 'login', 'sessao', 42, 'testuser_e2e', 42, 'testuser_e2e', 'operador', '[]', '[]', '::1', '', '2026-03-20 10:53:29.205905-03');
INSERT INTO public.tb_auditoria VALUES (306, 'logout', 'sessao', NULL, NULL, 42, 'testuser_e2e', 'operador', '[]', '[]', '::1', '', '2026-03-20 10:53:29.306106-03');
INSERT INTO public.tb_auditoria VALUES (307, 'criar', 'conexao', 34, '', 1, 'admin', 'super_admin', '[]', '[]', '::1', '', '2026-03-20 10:53:30.06709-03');
INSERT INTO public.tb_auditoria VALUES (308, 'excluir', 'conexao', 34, NULL, 1, 'admin', 'super_admin', '[]', '[]', '::1', '', '2026-03-20 10:53:36.472352-03');
INSERT INTO public.tb_auditoria VALUES (309, 'excluir', 'usuario', 42, 'testuser_e2e', 1, 'admin', 'super_admin', '[]', '[]', '::1', '', '2026-03-20 10:53:36.619409-03');
INSERT INTO public.tb_auditoria VALUES (310, 'excluir', 'empresa', 17, 'Empresa Teste E2E', 1, 'admin', 'super_admin', '[]', '[]', '::1', '', '2026-03-20 10:53:36.917466-03');
INSERT INTO public.tb_auditoria VALUES (311, 'login', 'sessao', 1, 'admin', 1, 'admin', 'super_admin', '[]', '[]', '::1', '', '2026-03-20 10:53:42.461009-03');
INSERT INTO public.tb_auditoria VALUES (312, 'login', 'sessao', 1, 'admin', 1, 'admin', 'super_admin', '[]', '[]', '::1', '', '2026-03-20 10:53:50.810831-03');
INSERT INTO public.tb_auditoria VALUES (313, 'login', 'sessao', 1, 'admin', 1, 'admin', 'super_admin', '[]', '[]', '::1', '', '2026-03-20 10:54:19.914367-03');
INSERT INTO public.tb_auditoria VALUES (314, 'login', 'sessao', 1, 'admin', 1, 'admin', 'super_admin', '[]', '[]', '::1', '', '2026-03-20 10:54:26.575012-03');
INSERT INTO public.tb_auditoria VALUES (315, 'login', 'sessao', 43, 'test_dev', 43, 'test_dev', 'desenvolvedor', '[]', '[]', '::1', '', '2026-03-20 10:54:26.741913-03');
INSERT INTO public.tb_auditoria VALUES (316, 'login', 'sessao', 44, 'test_op', 44, 'test_op', 'operador', '[]', '[]', '::1', '', '2026-03-20 10:54:26.871863-03');
INSERT INTO public.tb_auditoria VALUES (317, 'login', 'sessao', 1, 'admin', 1, 'admin', 'super_admin', '[]', '[]', '::1', '', '2026-03-20 10:54:35.963053-03');
INSERT INTO public.tb_auditoria VALUES (318, 'login', 'sessao', 1, 'admin', 1, 'admin', 'super_admin', '[]', '[]', '::1', '', '2026-03-20 10:54:40.785822-03');
INSERT INTO public.tb_auditoria VALUES (319, 'editar', 'configuracao', NULL, 'Grupo: geral', 1, 'admin', 'super_admin', '{"app_nome": "DMC DataLoad"}', '{"app_nome": "DMC DataLoad"}', '::1', '', '2026-03-20 10:54:41.344507-03');
INSERT INTO public.tb_auditoria VALUES (320, 'login', 'sessao', 1, 'admin', 1, 'admin', 'super_admin', '[]', '[]', '::1', '', '2026-03-20 10:54:43.59279-03');
INSERT INTO public.tb_auditoria VALUES (321, 'criar', 'pipeline', 23, 'Pipeline Teste E2E', 1, 'admin', 'super_admin', '[]', '[]', '::1', '', '2026-03-20 10:54:43.826684-03');
INSERT INTO public.tb_auditoria VALUES (322, 'excluir', 'pipeline', 23, NULL, 1, 'admin', 'super_admin', '[]', '[]', '::1', '', '2026-03-20 10:54:44.036804-03');
INSERT INTO public.tb_auditoria VALUES (323, 'criar', 'workflow', 15, 'Workflow Teste E2E', 1, 'admin', 'super_admin', '[]', '[]', '::1', '', '2026-03-20 10:54:44.266047-03');
INSERT INTO public.tb_auditoria VALUES (324, 'excluir', 'workflow', 15, NULL, 1, 'admin', 'super_admin', '[]', '[]', '::1', '', '2026-03-20 10:54:44.617702-03');
INSERT INTO public.tb_auditoria VALUES (325, 'logout', 'sessao', NULL, NULL, 23, 'renan', 'operador', '[]', '[]', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-20 10:54:51.176668-03');
INSERT INTO public.tb_auditoria VALUES (326, 'login', 'sessao', 23, 'renan', 23, 'renan', 'operador', '[]', '[]', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-20 10:54:54.247655-03');
INSERT INTO public.tb_auditoria VALUES (327, 'login', 'sessao', 1, 'admin', 1, 'admin', 'super_admin', '[]', '[]', '::1', 'DMC-BrowserTest/2.0 (Simulated Browser)', '2026-03-20 11:06:16.572868-03');
INSERT INTO public.tb_auditoria VALUES (328, 'criar', 'empresa', 18, 'DMC Tecnologia', 1, 'admin', 'super_admin', '[]', '[]', '::1', 'DMC-BrowserTest/2.0 (Simulated Browser)', '2026-03-20 11:06:16.784908-03');
INSERT INTO public.tb_auditoria VALUES (329, 'criar', 'empresa', 19, 'Alpha Corp', 1, 'admin', 'super_admin', '[]', '[]', '::1', 'DMC-BrowserTest/2.0 (Simulated Browser)', '2026-03-20 11:06:16.96141-03');
INSERT INTO public.tb_auditoria VALUES (330, 'criar', 'empresa', 20, 'Beta Sistemas', 1, 'admin', 'super_admin', '[]', '[]', '::1', 'DMC-BrowserTest/2.0 (Simulated Browser)', '2026-03-20 11:06:17.111367-03');
INSERT INTO public.tb_auditoria VALUES (331, 'criar', 'usuario', 45, 'admin_browser_test', 1, 'admin', 'super_admin', '[]', '{"nivel_acesso": "admin"}', '::1', 'DMC-BrowserTest/2.0 (Simulated Browser)', '2026-03-20 11:06:18.228085-03');
INSERT INTO public.tb_auditoria VALUES (332, 'criar', 'usuario', 46, 'dev_browser_test', 1, 'admin', 'super_admin', '[]', '{"nivel_acesso": "desenvolvedor"}', '::1', 'DMC-BrowserTest/2.0 (Simulated Browser)', '2026-03-20 11:06:18.493528-03');
INSERT INTO public.tb_auditoria VALUES (333, 'criar', 'usuario', 47, 'op_browser_test', 1, 'admin', 'super_admin', '[]', '{"nivel_acesso": "operador"}', '::1', 'DMC-BrowserTest/2.0 (Simulated Browser)', '2026-03-20 11:06:18.718585-03');
INSERT INTO public.tb_auditoria VALUES (334, 'login', 'sessao', 1, 'admin', 1, 'admin', 'super_admin', '[]', '[]', '::1', 'DMC-BrowserTest/2.0 (Simulated Browser)', '2026-03-20 11:06:25.707552-03');
INSERT INTO public.tb_auditoria VALUES (335, 'criar', 'conexao', 35, '', 1, 'admin', 'super_admin', '[]', '[]', '::1', 'DMC-BrowserTest/2.0 (Simulated Browser)', '2026-03-20 11:06:32.839344-03');
INSERT INTO public.tb_auditoria VALUES (336, 'login', 'sessao', 45, 'admin_browser_test', 45, 'admin_browser_test', 'admin', '[]', '[]', '::1', 'DMC-BrowserTest/2.0 (Simulated Browser)', '2026-03-20 11:06:33.658208-03');
INSERT INTO public.tb_auditoria VALUES (337, 'criar', 'conexao', 36, '', 45, 'admin_browser_test', 'admin', '[]', '[]', '::1', 'DMC-BrowserTest/2.0 (Simulated Browser)', '2026-03-20 11:06:35.251028-03');
INSERT INTO public.tb_auditoria VALUES (338, 'login', 'sessao', 46, 'dev_browser_test', 46, 'dev_browser_test', 'desenvolvedor', '[]', '[]', '::1', 'DMC-BrowserTest/2.0 (Simulated Browser)', '2026-03-20 11:06:35.644298-03');
INSERT INTO public.tb_auditoria VALUES (339, 'criar', 'conexao', 37, '', 46, 'dev_browser_test', 'desenvolvedor', '[]', '[]', '::1', 'DMC-BrowserTest/2.0 (Simulated Browser)', '2026-03-20 11:06:37.121013-03');
INSERT INTO public.tb_auditoria VALUES (340, 'login', 'sessao', 47, 'op_browser_test', 47, 'op_browser_test', 'operador', '[]', '[]', '::1', 'DMC-BrowserTest/2.0 (Simulated Browser)', '2026-03-20 11:06:37.606274-03');
INSERT INTO public.tb_auditoria VALUES (341, 'excluir', 'conexao', 35, NULL, 1, 'admin', 'super_admin', '[]', '[]', '::1', 'DMC-BrowserTest/2.0 (Simulated Browser)', '2026-03-20 11:06:43.101419-03');
INSERT INTO public.tb_auditoria VALUES (342, 'excluir', 'conexao', 36, NULL, 1, 'admin', 'super_admin', '[]', '[]', '::1', 'DMC-BrowserTest/2.0 (Simulated Browser)', '2026-03-20 11:06:44.489521-03');
INSERT INTO public.tb_auditoria VALUES (343, 'excluir', 'conexao', 37, NULL, 1, 'admin', 'super_admin', '[]', '[]', '::1', 'DMC-BrowserTest/2.0 (Simulated Browser)', '2026-03-20 11:06:44.951931-03');
INSERT INTO public.tb_auditoria VALUES (344, 'login', 'sessao', 1, 'admin', 1, 'admin', 'super_admin', '[]', '[]', '::1', 'DMC-BrowserTest/2.0 (Simulated Browser)', '2026-03-20 11:06:52.547372-03');
INSERT INTO public.tb_auditoria VALUES (345, 'criar', 'conexao', 38, '', 1, 'admin', 'super_admin', '[]', '[]', '::1', 'DMC-BrowserTest/2.0 (Simulated Browser)', '2026-03-20 11:06:58.963492-03');
INSERT INTO public.tb_auditoria VALUES (346, 'login', 'sessao', 45, 'admin_browser_test', 45, 'admin_browser_test', 'admin', '[]', '[]', '::1', 'DMC-BrowserTest/2.0 (Simulated Browser)', '2026-03-20 11:07:00.008222-03');
INSERT INTO public.tb_auditoria VALUES (347, 'criar', 'conexao', 39, '', 45, 'admin_browser_test', 'admin', '[]', '[]', '::1', 'DMC-BrowserTest/2.0 (Simulated Browser)', '2026-03-20 11:07:01.649745-03');
INSERT INTO public.tb_auditoria VALUES (348, 'login', 'sessao', 46, 'dev_browser_test', 46, 'dev_browser_test', 'desenvolvedor', '[]', '[]', '::1', 'DMC-BrowserTest/2.0 (Simulated Browser)', '2026-03-20 11:07:02.032207-03');
INSERT INTO public.tb_auditoria VALUES (349, 'criar', 'conexao', 40, '', 46, 'dev_browser_test', 'desenvolvedor', '[]', '[]', '::1', 'DMC-BrowserTest/2.0 (Simulated Browser)', '2026-03-20 11:07:03.64139-03');
INSERT INTO public.tb_auditoria VALUES (350, 'login', 'sessao', 47, 'op_browser_test', 47, 'op_browser_test', 'operador', '[]', '[]', '::1', 'DMC-BrowserTest/2.0 (Simulated Browser)', '2026-03-20 11:07:04.265744-03');
INSERT INTO public.tb_auditoria VALUES (351, 'excluir', 'conexao', 38, NULL, 1, 'admin', 'super_admin', '[]', '[]', '::1', 'DMC-BrowserTest/2.0 (Simulated Browser)', '2026-03-20 11:07:09.530136-03');
INSERT INTO public.tb_auditoria VALUES (352, 'excluir', 'conexao', 39, NULL, 1, 'admin', 'super_admin', '[]', '[]', '::1', 'DMC-BrowserTest/2.0 (Simulated Browser)', '2026-03-20 11:07:10.127995-03');
INSERT INTO public.tb_auditoria VALUES (353, 'excluir', 'conexao', 40, NULL, 1, 'admin', 'super_admin', '[]', '[]', '::1', 'DMC-BrowserTest/2.0 (Simulated Browser)', '2026-03-20 11:07:10.647762-03');
INSERT INTO public.tb_auditoria VALUES (354, 'login', 'sessao', 1, 'admin', 1, 'admin', 'super_admin', '[]', '[]', '::1', 'DMC-BrowserTest/2.0 (Simulated Browser)', '2026-03-20 11:07:16.134581-03');
INSERT INTO public.tb_auditoria VALUES (355, 'criar', 'conexao', 41, '', 1, 'admin', 'super_admin', '[]', '[]', '::1', 'DMC-BrowserTest/2.0 (Simulated Browser)', '2026-03-20 11:07:22.330731-03');
INSERT INTO public.tb_auditoria VALUES (356, 'login', 'sessao', 45, 'admin_browser_test', 45, 'admin_browser_test', 'admin', '[]', '[]', '::1', 'DMC-BrowserTest/2.0 (Simulated Browser)', '2026-03-20 11:07:23.315329-03');
INSERT INTO public.tb_auditoria VALUES (357, 'criar', 'conexao', 42, '', 45, 'admin_browser_test', 'admin', '[]', '[]', '::1', 'DMC-BrowserTest/2.0 (Simulated Browser)', '2026-03-20 11:07:25.061831-03');
INSERT INTO public.tb_auditoria VALUES (358, 'login', 'sessao', 46, 'dev_browser_test', 46, 'dev_browser_test', 'desenvolvedor', '[]', '[]', '::1', 'DMC-BrowserTest/2.0 (Simulated Browser)', '2026-03-20 11:07:25.576504-03');
INSERT INTO public.tb_auditoria VALUES (359, 'criar', 'conexao', 43, '', 46, 'dev_browser_test', 'desenvolvedor', '[]', '[]', '::1', 'DMC-BrowserTest/2.0 (Simulated Browser)', '2026-03-20 11:07:27.155876-03');
INSERT INTO public.tb_auditoria VALUES (360, 'login', 'sessao', 47, 'op_browser_test', 47, 'op_browser_test', 'operador', '[]', '[]', '::1', 'DMC-BrowserTest/2.0 (Simulated Browser)', '2026-03-20 11:07:27.670034-03');
INSERT INTO public.tb_auditoria VALUES (361, 'excluir', 'conexao', 41, NULL, 1, 'admin', 'super_admin', '[]', '[]', '::1', 'DMC-BrowserTest/2.0 (Simulated Browser)', '2026-03-20 11:07:33.365752-03');
INSERT INTO public.tb_auditoria VALUES (362, 'excluir', 'conexao', 42, NULL, 1, 'admin', 'super_admin', '[]', '[]', '::1', 'DMC-BrowserTest/2.0 (Simulated Browser)', '2026-03-20 11:07:33.999734-03');
INSERT INTO public.tb_auditoria VALUES (363, 'excluir', 'conexao', 43, NULL, 1, 'admin', 'super_admin', '[]', '[]', '::1', 'DMC-BrowserTest/2.0 (Simulated Browser)', '2026-03-20 11:07:34.526607-03');
INSERT INTO public.tb_auditoria VALUES (364, 'login', 'sessao', 1, 'admin', 1, 'admin', 'super_admin', '[]', '[]', '::1', 'DMC-BrowserTest/2.0 (Simulated Browser)', '2026-03-20 11:07:39.45623-03');
INSERT INTO public.tb_auditoria VALUES (365, 'criar', 'conexao', 44, '', 1, 'admin', 'super_admin', '[]', '[]', '::1', 'DMC-BrowserTest/2.0 (Simulated Browser)', '2026-03-20 11:07:44.152339-03');
INSERT INTO public.tb_auditoria VALUES (366, 'login', 'sessao', 45, 'admin_browser_test', 45, 'admin_browser_test', 'admin', '[]', '[]', '::1', 'DMC-BrowserTest/2.0 (Simulated Browser)', '2026-03-20 11:07:44.912025-03');
INSERT INTO public.tb_auditoria VALUES (367, 'criar', 'conexao', 45, '', 45, 'admin_browser_test', 'admin', '[]', '[]', '::1', 'DMC-BrowserTest/2.0 (Simulated Browser)', '2026-03-20 11:07:46.335922-03');
INSERT INTO public.tb_auditoria VALUES (368, 'login', 'sessao', 46, 'dev_browser_test', 46, 'dev_browser_test', 'desenvolvedor', '[]', '[]', '::1', 'DMC-BrowserTest/2.0 (Simulated Browser)', '2026-03-20 11:07:46.712962-03');
INSERT INTO public.tb_auditoria VALUES (369, 'criar', 'conexao', 46, '', 46, 'dev_browser_test', 'desenvolvedor', '[]', '[]', '::1', 'DMC-BrowserTest/2.0 (Simulated Browser)', '2026-03-20 11:07:48.14082-03');
INSERT INTO public.tb_auditoria VALUES (370, 'login', 'sessao', 47, 'op_browser_test', 47, 'op_browser_test', 'operador', '[]', '[]', '::1', 'DMC-BrowserTest/2.0 (Simulated Browser)', '2026-03-20 11:07:48.646686-03');
INSERT INTO public.tb_auditoria VALUES (371, 'excluir', 'conexao', 44, NULL, 1, 'admin', 'super_admin', '[]', '[]', '::1', 'DMC-BrowserTest/2.0 (Simulated Browser)', '2026-03-20 11:07:53.520235-03');
INSERT INTO public.tb_auditoria VALUES (372, 'excluir', 'conexao', 45, NULL, 1, 'admin', 'super_admin', '[]', '[]', '::1', 'DMC-BrowserTest/2.0 (Simulated Browser)', '2026-03-20 11:07:53.88175-03');
INSERT INTO public.tb_auditoria VALUES (373, 'excluir', 'conexao', 46, NULL, 1, 'admin', 'super_admin', '[]', '[]', '::1', 'DMC-BrowserTest/2.0 (Simulated Browser)', '2026-03-20 11:07:54.347544-03');
INSERT INTO public.tb_auditoria VALUES (374, 'editar', 'conexao', 11, '', 1, 'admin', 'super_admin', '[]', '[]', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-20 11:07:54.764097-03');
INSERT INTO public.tb_auditoria VALUES (375, 'login', 'sessao', 1, 'admin', 1, 'admin', 'super_admin', '[]', '[]', '::1', 'DMC-BrowserTest/2.0 (Simulated Browser)', '2026-03-20 11:08:01.122132-03');
INSERT INTO public.tb_auditoria VALUES (376, 'criar', 'conexao', 47, '', 1, 'admin', 'super_admin', '[]', '[]', '::1', 'DMC-BrowserTest/2.0 (Simulated Browser)', '2026-03-20 11:08:06.71059-03');
INSERT INTO public.tb_auditoria VALUES (377, 'login', 'sessao', 45, 'admin_browser_test', 45, 'admin_browser_test', 'admin', '[]', '[]', '::1', 'DMC-BrowserTest/2.0 (Simulated Browser)', '2026-03-20 11:08:07.49437-03');
INSERT INTO public.tb_auditoria VALUES (378, 'criar', 'conexao', 48, '', 45, 'admin_browser_test', 'admin', '[]', '[]', '::1', 'DMC-BrowserTest/2.0 (Simulated Browser)', '2026-03-20 11:08:08.78271-03');
INSERT INTO public.tb_auditoria VALUES (379, 'login', 'sessao', 46, 'dev_browser_test', 46, 'dev_browser_test', 'desenvolvedor', '[]', '[]', '::1', 'DMC-BrowserTest/2.0 (Simulated Browser)', '2026-03-20 11:08:09.14494-03');
INSERT INTO public.tb_auditoria VALUES (380, 'criar', 'conexao', 49, '', 46, 'dev_browser_test', 'desenvolvedor', '[]', '[]', '::1', 'DMC-BrowserTest/2.0 (Simulated Browser)', '2026-03-20 11:08:10.401018-03');
INSERT INTO public.tb_auditoria VALUES (381, 'login', 'sessao', 47, 'op_browser_test', 47, 'op_browser_test', 'operador', '[]', '[]', '::1', 'DMC-BrowserTest/2.0 (Simulated Browser)', '2026-03-20 11:08:10.874602-03');
INSERT INTO public.tb_auditoria VALUES (382, 'excluir', 'conexao', 47, NULL, 1, 'admin', 'super_admin', '[]', '[]', '::1', 'DMC-BrowserTest/2.0 (Simulated Browser)', '2026-03-20 11:08:14.962166-03');
INSERT INTO public.tb_auditoria VALUES (383, 'excluir', 'conexao', 48, NULL, 1, 'admin', 'super_admin', '[]', '[]', '::1', 'DMC-BrowserTest/2.0 (Simulated Browser)', '2026-03-20 11:08:15.390547-03');
INSERT INTO public.tb_auditoria VALUES (384, 'excluir', 'conexao', 49, NULL, 1, 'admin', 'super_admin', '[]', '[]', '::1', 'DMC-BrowserTest/2.0 (Simulated Browser)', '2026-03-20 11:08:15.78086-03');
INSERT INTO public.tb_auditoria VALUES (385, 'login', 'sessao', 1, 'admin', 1, 'admin', 'super_admin', '[]', '[]', '::1', 'DMC-BrowserTest/2.0 (Simulated Browser)', '2026-03-20 11:08:24.989256-03');
INSERT INTO public.tb_auditoria VALUES (386, 'criar', 'conexao', 50, '', 1, 'admin', 'super_admin', '[]', '[]', '::1', 'DMC-BrowserTest/2.0 (Simulated Browser)', '2026-03-20 11:08:30.905825-03');
INSERT INTO public.tb_auditoria VALUES (387, 'login', 'sessao', 45, 'admin_browser_test', 45, 'admin_browser_test', 'admin', '[]', '[]', '::1', 'DMC-BrowserTest/2.0 (Simulated Browser)', '2026-03-20 11:08:31.74438-03');
INSERT INTO public.tb_auditoria VALUES (388, 'criar', 'conexao', 51, '', 45, 'admin_browser_test', 'admin', '[]', '[]', '::1', 'DMC-BrowserTest/2.0 (Simulated Browser)', '2026-03-20 11:08:33.144675-03');
INSERT INTO public.tb_auditoria VALUES (389, 'login', 'sessao', 46, 'dev_browser_test', 46, 'dev_browser_test', 'desenvolvedor', '[]', '[]', '::1', 'DMC-BrowserTest/2.0 (Simulated Browser)', '2026-03-20 11:08:33.571401-03');
INSERT INTO public.tb_auditoria VALUES (390, 'criar', 'conexao', 52, '', 46, 'dev_browser_test', 'desenvolvedor', '[]', '[]', '::1', 'DMC-BrowserTest/2.0 (Simulated Browser)', '2026-03-20 11:08:35.39703-03');
INSERT INTO public.tb_auditoria VALUES (391, 'login', 'sessao', 47, 'op_browser_test', 47, 'op_browser_test', 'operador', '[]', '[]', '::1', 'DMC-BrowserTest/2.0 (Simulated Browser)', '2026-03-20 11:08:35.928866-03');
INSERT INTO public.tb_auditoria VALUES (392, 'excluir', 'conexao', 50, NULL, 1, 'admin', 'super_admin', '[]', '[]', '::1', 'DMC-BrowserTest/2.0 (Simulated Browser)', '2026-03-20 11:08:40.277614-03');
INSERT INTO public.tb_auditoria VALUES (393, 'excluir', 'conexao', 51, NULL, 1, 'admin', 'super_admin', '[]', '[]', '::1', 'DMC-BrowserTest/2.0 (Simulated Browser)', '2026-03-20 11:08:40.727394-03');
INSERT INTO public.tb_auditoria VALUES (394, 'excluir', 'conexao', 52, NULL, 1, 'admin', 'super_admin', '[]', '[]', '::1', 'DMC-BrowserTest/2.0 (Simulated Browser)', '2026-03-20 11:08:41.22807-03');
INSERT INTO public.tb_auditoria VALUES (395, 'login', 'sessao', 1, 'admin', 1, 'admin', 'super_admin', '[]', '[]', '::1', '', '2026-03-20 11:09:08.038591-03');
INSERT INTO public.tb_auditoria VALUES (397, 'criar', 'empresa', 21, 'Empresa Teste E2E', 1, 'admin', 'super_admin', '[]', '[]', '::1', '', '2026-03-20 11:09:16.275693-03');
INSERT INTO public.tb_auditoria VALUES (398, 'criar', 'usuario', 48, 'testuser_e2e', 1, 'admin', 'super_admin', '[]', '{"nivel_acesso": "operador"}', '::1', '', '2026-03-20 11:09:16.847002-03');
INSERT INTO public.tb_auditoria VALUES (399, 'login', 'sessao', 48, 'testuser_e2e', 48, 'testuser_e2e', 'operador', '[]', '[]', '::1', '', '2026-03-20 11:09:17.284833-03');
INSERT INTO public.tb_auditoria VALUES (400, 'logout', 'sessao', NULL, NULL, 48, 'testuser_e2e', 'operador', '[]', '[]', '::1', '', '2026-03-20 11:09:17.480236-03');
INSERT INTO public.tb_auditoria VALUES (401, 'login', 'sessao', 48, 'testuser_e2e', 48, 'testuser_e2e', 'operador', '[]', '[]', '::1', '', '2026-03-20 11:09:17.886485-03');
INSERT INTO public.tb_auditoria VALUES (402, 'logout', 'sessao', NULL, NULL, 48, 'testuser_e2e', 'operador', '[]', '[]', '::1', '', '2026-03-20 11:09:17.998247-03');
INSERT INTO public.tb_auditoria VALUES (403, 'criar', 'conexao', 53, '', 1, 'admin', 'super_admin', '[]', '[]', '::1', '', '2026-03-20 11:09:18.732428-03');
INSERT INTO public.tb_auditoria VALUES (404, 'excluir', 'conexao', 53, NULL, 1, 'admin', 'super_admin', '[]', '[]', '::1', '', '2026-03-20 11:09:23.539873-03');
INSERT INTO public.tb_auditoria VALUES (405, 'excluir', 'usuario', 48, 'testuser_e2e', 1, 'admin', 'super_admin', '[]', '[]', '::1', '', '2026-03-20 11:09:23.696467-03');
INSERT INTO public.tb_auditoria VALUES (406, 'excluir', 'empresa', 21, 'Empresa Teste E2E', 1, 'admin', 'super_admin', '[]', '[]', '::1', '', '2026-03-20 11:09:23.994654-03');
INSERT INTO public.tb_auditoria VALUES (407, 'login', 'sessao', 1, 'admin', 1, 'admin', 'super_admin', '[]', '[]', '::1', '', '2026-03-20 11:09:30.659264-03');
INSERT INTO public.tb_auditoria VALUES (408, 'login', 'sessao', 1, 'admin', 1, 'admin', 'super_admin', '[]', '[]', '::1', '', '2026-03-20 11:09:35.573601-03');
INSERT INTO public.tb_auditoria VALUES (409, 'login', 'sessao', 49, 'test_dev', 49, 'test_dev', 'desenvolvedor', '[]', '[]', '::1', '', '2026-03-20 11:09:35.781275-03');
INSERT INTO public.tb_auditoria VALUES (410, 'login', 'sessao', 50, 'test_op', 50, 'test_op', 'operador', '[]', '[]', '::1', '', '2026-03-20 11:09:35.967833-03');
INSERT INTO public.tb_auditoria VALUES (411, 'login', 'sessao', 1, 'admin', 1, 'admin', 'super_admin', '[]', '[]', '::1', '', '2026-03-20 11:09:40.520793-03');
INSERT INTO public.tb_auditoria VALUES (412, 'criar', 'pipeline', 24, 'Pipeline Teste E2E', 1, 'admin', 'super_admin', '[]', '[]', '::1', '', '2026-03-20 11:09:40.812714-03');
INSERT INTO public.tb_auditoria VALUES (413, 'excluir', 'pipeline', 24, NULL, 1, 'admin', 'super_admin', '[]', '[]', '::1', '', '2026-03-20 11:09:41.087494-03');
INSERT INTO public.tb_auditoria VALUES (414, 'criar', 'workflow', 16, 'Workflow Teste E2E', 1, 'admin', 'super_admin', '[]', '[]', '::1', '', '2026-03-20 11:09:41.430569-03');
INSERT INTO public.tb_auditoria VALUES (415, 'excluir', 'workflow', 16, NULL, 1, 'admin', 'super_admin', '[]', '[]', '::1', '', '2026-03-20 11:09:41.823733-03');
INSERT INTO public.tb_auditoria VALUES (416, 'login', 'sessao', 1, 'admin', 1, 'admin', 'super_admin', '[]', '[]', '::1', '', '2026-03-20 11:09:43.922984-03');
INSERT INTO public.tb_auditoria VALUES (417, 'editar', 'configuracao', NULL, 'Grupo: geral', 1, 'admin', 'super_admin', '{"app_nome": "DMC DataLoad"}', '{"app_nome": "DMC DataLoad"}', '::1', '', '2026-03-20 11:09:44.6019-03');
INSERT INTO public.tb_auditoria VALUES (418, 'login', 'sessao', 1, 'admin', 1, 'admin', 'super_admin', '[]', '[]', '::1', 'DMC-BrowserTest/2.0 (Simulated Browser)', '2026-03-20 11:11:46.033448-03');
INSERT INTO public.tb_auditoria VALUES (419, 'criar', 'conexao', 54, '', 1, 'admin', 'super_admin', '[]', '[]', '::1', 'DMC-BrowserTest/2.0 (Simulated Browser)', '2026-03-20 11:11:52.381863-03');
INSERT INTO public.tb_auditoria VALUES (420, 'login', 'sessao', 45, 'admin_browser_test', 45, 'admin_browser_test', 'admin', '[]', '[]', '::1', 'DMC-BrowserTest/2.0 (Simulated Browser)', '2026-03-20 11:11:53.27077-03');
INSERT INTO public.tb_auditoria VALUES (421, 'criar', 'conexao', 55, '', 45, 'admin_browser_test', 'admin', '[]', '[]', '::1', 'DMC-BrowserTest/2.0 (Simulated Browser)', '2026-03-20 11:11:54.776896-03');
INSERT INTO public.tb_auditoria VALUES (422, 'login', 'sessao', 46, 'dev_browser_test', 46, 'dev_browser_test', 'desenvolvedor', '[]', '[]', '::1', 'DMC-BrowserTest/2.0 (Simulated Browser)', '2026-03-20 11:11:55.160562-03');
INSERT INTO public.tb_auditoria VALUES (423, 'criar', 'conexao', 56, '', 46, 'dev_browser_test', 'desenvolvedor', '[]', '[]', '::1', 'DMC-BrowserTest/2.0 (Simulated Browser)', '2026-03-20 11:11:56.518253-03');
INSERT INTO public.tb_auditoria VALUES (424, 'login', 'sessao', 47, 'op_browser_test', 47, 'op_browser_test', 'operador', '[]', '[]', '::1', 'DMC-BrowserTest/2.0 (Simulated Browser)', '2026-03-20 11:11:57.173744-03');
INSERT INTO public.tb_auditoria VALUES (425, 'excluir', 'conexao', 54, NULL, 1, 'admin', 'super_admin', '[]', '[]', '::1', 'DMC-BrowserTest/2.0 (Simulated Browser)', '2026-03-20 11:12:03.032586-03');
INSERT INTO public.tb_auditoria VALUES (426, 'excluir', 'conexao', 55, NULL, 1, 'admin', 'super_admin', '[]', '[]', '::1', 'DMC-BrowserTest/2.0 (Simulated Browser)', '2026-03-20 11:12:03.454786-03');
INSERT INTO public.tb_auditoria VALUES (427, 'excluir', 'conexao', 56, NULL, 1, 'admin', 'super_admin', '[]', '[]', '::1', 'DMC-BrowserTest/2.0 (Simulated Browser)', '2026-03-20 11:12:03.887177-03');
INSERT INTO public.tb_auditoria VALUES (428, 'excluir', 'usuario', 45, 'admin_browser_test', 1, 'admin', 'super_admin', '[]', '[]', '::1', 'DMC-BrowserTest/2.0 (Simulated Browser)', '2026-03-20 11:12:04.042545-03');
INSERT INTO public.tb_auditoria VALUES (429, 'excluir', 'usuario', 46, 'dev_browser_test', 1, 'admin', 'super_admin', '[]', '[]', '::1', 'DMC-BrowserTest/2.0 (Simulated Browser)', '2026-03-20 11:12:04.192524-03');
INSERT INTO public.tb_auditoria VALUES (430, 'excluir', 'usuario', 47, 'op_browser_test', 1, 'admin', 'super_admin', '[]', '[]', '::1', 'DMC-BrowserTest/2.0 (Simulated Browser)', '2026-03-20 11:12:04.361962-03');
INSERT INTO public.tb_auditoria VALUES (431, 'excluir', 'empresa', 18, 'DMC Tecnologia', 1, 'admin', 'super_admin', '[]', '[]', '::1', 'DMC-BrowserTest/2.0 (Simulated Browser)', '2026-03-20 11:12:05.565236-03');
INSERT INTO public.tb_auditoria VALUES (432, 'excluir', 'empresa', 19, 'Alpha Corp', 1, 'admin', 'super_admin', '[]', '[]', '::1', 'DMC-BrowserTest/2.0 (Simulated Browser)', '2026-03-20 11:12:05.790898-03');
INSERT INTO public.tb_auditoria VALUES (433, 'excluir', 'empresa', 20, 'Beta Sistemas', 1, 'admin', 'super_admin', '[]', '[]', '::1', 'DMC-BrowserTest/2.0 (Simulated Browser)', '2026-03-20 11:12:05.951781-03');
INSERT INTO public.tb_auditoria VALUES (434, 'login', 'sessao', 1, 'admin', 1, 'admin', 'super_admin', '[]', '[]', '::1', 'DMC-BrowserTest/2.0 (Simulated Browser)', '2026-03-20 11:12:19.46862-03');
INSERT INTO public.tb_auditoria VALUES (435, 'criar', 'empresa', 22, 'DMC Tecnologia', 1, 'admin', 'super_admin', '[]', '[]', '::1', 'DMC-BrowserTest/2.0 (Simulated Browser)', '2026-03-20 11:12:19.798157-03');
INSERT INTO public.tb_auditoria VALUES (436, 'criar', 'empresa', 23, 'Alpha Corp', 1, 'admin', 'super_admin', '[]', '[]', '::1', 'DMC-BrowserTest/2.0 (Simulated Browser)', '2026-03-20 11:12:19.937819-03');
INSERT INTO public.tb_auditoria VALUES (437, 'criar', 'empresa', 24, 'Beta Sistemas', 1, 'admin', 'super_admin', '[]', '[]', '::1', 'DMC-BrowserTest/2.0 (Simulated Browser)', '2026-03-20 11:12:20.108696-03');
INSERT INTO public.tb_auditoria VALUES (438, 'criar', 'usuario', 51, 'admin_browser_test', 1, 'admin', 'super_admin', '[]', '{"nivel_acesso": "admin"}', '::1', 'DMC-BrowserTest/2.0 (Simulated Browser)', '2026-03-20 11:12:21.284454-03');
INSERT INTO public.tb_auditoria VALUES (439, 'criar', 'usuario', 52, 'dev_browser_test', 1, 'admin', 'super_admin', '[]', '{"nivel_acesso": "desenvolvedor"}', '::1', 'DMC-BrowserTest/2.0 (Simulated Browser)', '2026-03-20 11:12:21.486054-03');
INSERT INTO public.tb_auditoria VALUES (440, 'criar', 'usuario', 53, 'op_browser_test', 1, 'admin', 'super_admin', '[]', '{"nivel_acesso": "operador"}', '::1', 'DMC-BrowserTest/2.0 (Simulated Browser)', '2026-03-20 11:12:21.696904-03');
INSERT INTO public.tb_auditoria VALUES (441, 'criar', 'conexao', 57, '', 1, 'admin', 'super_admin', '[]', '[]', '::1', 'DMC-BrowserTest/2.0 (Simulated Browser)', '2026-03-20 11:12:26.924491-03');
INSERT INTO public.tb_auditoria VALUES (442, 'login', 'sessao', 51, 'admin_browser_test', 51, 'admin_browser_test', 'admin', '[]', '[]', '::1', 'DMC-BrowserTest/2.0 (Simulated Browser)', '2026-03-20 11:12:27.747455-03');
INSERT INTO public.tb_auditoria VALUES (443, 'criar', 'conexao', 58, '', 51, 'admin_browser_test', 'admin', '[]', '[]', '::1', 'DMC-BrowserTest/2.0 (Simulated Browser)', '2026-03-20 11:12:29.360503-03');
INSERT INTO public.tb_auditoria VALUES (444, 'login', 'sessao', 52, 'dev_browser_test', 52, 'dev_browser_test', 'desenvolvedor', '[]', '[]', '::1', 'DMC-BrowserTest/2.0 (Simulated Browser)', '2026-03-20 11:12:29.754909-03');
INSERT INTO public.tb_auditoria VALUES (445, 'criar', 'conexao', 59, '', 52, 'dev_browser_test', 'desenvolvedor', '[]', '[]', '::1', 'DMC-BrowserTest/2.0 (Simulated Browser)', '2026-03-20 11:12:31.220141-03');
INSERT INTO public.tb_auditoria VALUES (446, 'login', 'sessao', 53, 'op_browser_test', 53, 'op_browser_test', 'operador', '[]', '[]', '::1', 'DMC-BrowserTest/2.0 (Simulated Browser)', '2026-03-20 11:12:31.745305-03');
INSERT INTO public.tb_auditoria VALUES (447, 'excluir', 'conexao', 57, NULL, 1, 'admin', 'super_admin', '[]', '[]', '::1', 'DMC-BrowserTest/2.0 (Simulated Browser)', '2026-03-20 11:12:36.075316-03');
INSERT INTO public.tb_auditoria VALUES (448, 'excluir', 'conexao', 58, NULL, 1, 'admin', 'super_admin', '[]', '[]', '::1', 'DMC-BrowserTest/2.0 (Simulated Browser)', '2026-03-20 11:12:36.510027-03');
INSERT INTO public.tb_auditoria VALUES (449, 'excluir', 'conexao', 59, NULL, 1, 'admin', 'super_admin', '[]', '[]', '::1', 'DMC-BrowserTest/2.0 (Simulated Browser)', '2026-03-20 11:12:36.969236-03');
INSERT INTO public.tb_auditoria VALUES (450, 'excluir', 'usuario', 51, 'admin_browser_test', 1, 'admin', 'super_admin', '[]', '[]', '::1', 'DMC-BrowserTest/2.0 (Simulated Browser)', '2026-03-20 11:12:37.101748-03');
INSERT INTO public.tb_auditoria VALUES (451, 'excluir', 'usuario', 52, 'dev_browser_test', 1, 'admin', 'super_admin', '[]', '[]', '::1', 'DMC-BrowserTest/2.0 (Simulated Browser)', '2026-03-20 11:12:37.248313-03');
INSERT INTO public.tb_auditoria VALUES (452, 'excluir', 'usuario', 53, 'op_browser_test', 1, 'admin', 'super_admin', '[]', '[]', '::1', 'DMC-BrowserTest/2.0 (Simulated Browser)', '2026-03-20 11:12:37.389315-03');
INSERT INTO public.tb_auditoria VALUES (453, 'excluir', 'empresa', 22, 'DMC Tecnologia', 1, 'admin', 'super_admin', '[]', '[]', '::1', 'DMC-BrowserTest/2.0 (Simulated Browser)', '2026-03-20 11:12:38.376735-03');
INSERT INTO public.tb_auditoria VALUES (454, 'excluir', 'empresa', 23, 'Alpha Corp', 1, 'admin', 'super_admin', '[]', '[]', '::1', 'DMC-BrowserTest/2.0 (Simulated Browser)', '2026-03-20 11:12:38.509785-03');
INSERT INTO public.tb_auditoria VALUES (455, 'excluir', 'empresa', 24, 'Beta Sistemas', 1, 'admin', 'super_admin', '[]', '[]', '::1', 'DMC-BrowserTest/2.0 (Simulated Browser)', '2026-03-20 11:12:38.647255-03');
INSERT INTO public.tb_auditoria VALUES (456, 'login', 'sessao', 1, 'admin', 1, 'admin', 'super_admin', '[]', '[]', '::1', '', '2026-03-20 11:12:47.850573-03');
INSERT INTO public.tb_auditoria VALUES (457, 'login', 'sessao', 1, 'admin', 1, 'admin', 'super_admin', '[]', '[]', '::1', '', '2026-03-20 11:12:49.710746-03');
INSERT INTO public.tb_auditoria VALUES (458, 'criar', 'empresa', 25, 'Empresa Teste E2E', 1, 'admin', 'super_admin', '[]', '[]', '::1', '', '2026-03-20 11:12:50.911107-03');
INSERT INTO public.tb_auditoria VALUES (459, 'criar', 'usuario', 54, 'testuser_e2e', 1, 'admin', 'super_admin', '[]', '{"nivel_acesso": "operador"}', '::1', '', '2026-03-20 11:12:51.53492-03');
INSERT INTO public.tb_auditoria VALUES (460, 'login', 'sessao', 54, 'testuser_e2e', 54, 'testuser_e2e', 'operador', '[]', '[]', '::1', '', '2026-03-20 11:12:51.999956-03');
INSERT INTO public.tb_auditoria VALUES (461, 'logout', 'sessao', NULL, NULL, 54, 'testuser_e2e', 'operador', '[]', '[]', '::1', '', '2026-03-20 11:12:52.201441-03');
INSERT INTO public.tb_auditoria VALUES (462, 'login', 'sessao', 54, 'testuser_e2e', 54, 'testuser_e2e', 'operador', '[]', '[]', '::1', '', '2026-03-20 11:12:52.705243-03');
INSERT INTO public.tb_auditoria VALUES (463, 'logout', 'sessao', NULL, NULL, 54, 'testuser_e2e', 'operador', '[]', '[]', '::1', '', '2026-03-20 11:12:52.856248-03');
INSERT INTO public.tb_auditoria VALUES (464, 'criar', 'conexao', 60, '', 1, 'admin', 'super_admin', '[]', '[]', '::1', '', '2026-03-20 11:12:53.422957-03');
INSERT INTO public.tb_auditoria VALUES (465, 'excluir', 'conexao', 60, NULL, 1, 'admin', 'super_admin', '[]', '[]', '::1', '', '2026-03-20 11:12:57.989902-03');
INSERT INTO public.tb_auditoria VALUES (466, 'excluir', 'usuario', 54, 'testuser_e2e', 1, 'admin', 'super_admin', '[]', '[]', '::1', '', '2026-03-20 11:12:58.126234-03');
INSERT INTO public.tb_auditoria VALUES (467, 'excluir', 'empresa', 25, 'Empresa Teste E2E', 1, 'admin', 'super_admin', '[]', '[]', '::1', '', '2026-03-20 11:12:58.388364-03');
INSERT INTO public.tb_auditoria VALUES (468, 'login', 'sessao', 1, 'admin', 1, 'admin', 'super_admin', '[]', '[]', '::1', '', '2026-03-20 11:12:58.767975-03');
INSERT INTO public.tb_auditoria VALUES (469, 'login', 'sessao', 1, 'admin', 1, 'admin', 'super_admin', '[]', '[]', '::1', '', '2026-03-20 11:13:03.223263-03');
INSERT INTO public.tb_auditoria VALUES (470, 'login', 'sessao', 55, 'test_dev', 55, 'test_dev', 'desenvolvedor', '[]', '[]', '::1', '', '2026-03-20 11:13:03.428362-03');
INSERT INTO public.tb_auditoria VALUES (471, 'login', 'sessao', 56, 'test_op', 56, 'test_op', 'operador', '[]', '[]', '::1', '', '2026-03-20 11:13:03.636402-03');
INSERT INTO public.tb_auditoria VALUES (472, 'login', 'sessao', 1, 'admin', 1, 'admin', 'super_admin', '[]', '[]', '::1', '', '2026-03-20 11:13:08.159605-03');
INSERT INTO public.tb_auditoria VALUES (473, 'criar', 'pipeline', 25, 'Pipeline Teste E2E', 1, 'admin', 'super_admin', '[]', '[]', '::1', '', '2026-03-20 11:13:08.522634-03');
INSERT INTO public.tb_auditoria VALUES (474, 'excluir', 'pipeline', 25, NULL, 1, 'admin', 'super_admin', '[]', '[]', '::1', '', '2026-03-20 11:13:08.792136-03');
INSERT INTO public.tb_auditoria VALUES (475, 'criar', 'workflow', 17, 'Workflow Teste E2E', 1, 'admin', 'super_admin', '[]', '[]', '::1', '', '2026-03-20 11:13:09.106038-03');
INSERT INTO public.tb_auditoria VALUES (476, 'excluir', 'workflow', 17, NULL, 1, 'admin', 'super_admin', '[]', '[]', '::1', '', '2026-03-20 11:13:09.51439-03');
INSERT INTO public.tb_auditoria VALUES (477, 'login', 'sessao', 1, 'admin', 1, 'admin', 'super_admin', '[]', '[]', '::1', '', '2026-03-20 11:13:11.346307-03');
INSERT INTO public.tb_auditoria VALUES (478, 'editar', 'configuracao', NULL, 'Grupo: geral', 1, 'admin', 'super_admin', '{"app_nome": "DMC DataLoad"}', '{"app_nome": "DMC DataLoad"}', '::1', '', '2026-03-20 11:13:11.836156-03');
INSERT INTO public.tb_auditoria VALUES (479, 'login', 'sessao', 1, 'admin', 1, 'admin', 'super_admin', '[]', '[]', '::1', 'DMC-Auditoria/3.0 (SimBrowser)', '2026-03-20 11:35:03.570375-03');
INSERT INTO public.tb_auditoria VALUES (480, 'criar', 'empresa', 26, 'Secretaria de TI', 1, 'admin', 'super_admin', '[]', '[]', '::1', 'DMC-Auditoria/3.0 (SimBrowser)', '2026-03-20 11:35:03.98328-03');
INSERT INTO public.tb_auditoria VALUES (481, 'criar', 'empresa', 27, 'Secretaria de Saúde', 1, 'admin', 'super_admin', '[]', '[]', '::1', 'DMC-Auditoria/3.0 (SimBrowser)', '2026-03-20 11:35:04.1346-03');
INSERT INTO public.tb_auditoria VALUES (482, 'criar', 'empresa', 28, 'Secretaria de Educação', 1, 'admin', 'super_admin', '[]', '[]', '::1', 'DMC-Auditoria/3.0 (SimBrowser)', '2026-03-20 11:35:04.312142-03');
INSERT INTO public.tb_auditoria VALUES (483, 'criar', 'empresa', 29, 'Secretaria de Finanças', 1, 'admin', 'super_admin', '[]', '[]', '::1', 'DMC-Auditoria/3.0 (SimBrowser)', '2026-03-20 11:35:04.468449-03');
INSERT INTO public.tb_auditoria VALUES (484, 'criar', 'empresa', 30, 'Secretaria de Obras', 1, 'admin', 'super_admin', '[]', '[]', '::1', 'DMC-Auditoria/3.0 (SimBrowser)', '2026-03-20 11:35:04.637205-03');
INSERT INTO public.tb_auditoria VALUES (485, 'criar', 'usuario', 57, 'audit_admin1', 1, 'admin', 'super_admin', '[]', '{"nivel_acesso": "admin"}', '::1', 'DMC-Auditoria/3.0 (SimBrowser)', '2026-03-20 11:35:07.190379-03');
INSERT INTO public.tb_auditoria VALUES (486, 'criar', 'usuario', 58, 'audit_admin2', 1, 'admin', 'super_admin', '[]', '{"nivel_acesso": "admin"}', '::1', 'DMC-Auditoria/3.0 (SimBrowser)', '2026-03-20 11:35:07.455199-03');
INSERT INTO public.tb_auditoria VALUES (487, 'criar', 'usuario', 59, 'audit_dev1', 1, 'admin', 'super_admin', '[]', '{"nivel_acesso": "desenvolvedor"}', '::1', 'DMC-Auditoria/3.0 (SimBrowser)', '2026-03-20 11:35:07.707526-03');
INSERT INTO public.tb_auditoria VALUES (488, 'criar', 'usuario', 60, 'audit_dev2', 1, 'admin', 'super_admin', '[]', '{"nivel_acesso": "desenvolvedor"}', '::1', 'DMC-Auditoria/3.0 (SimBrowser)', '2026-03-20 11:35:07.975544-03');
INSERT INTO public.tb_auditoria VALUES (489, 'criar', 'usuario', 61, 'audit_op1', 1, 'admin', 'super_admin', '[]', '{"nivel_acesso": "operador"}', '::1', 'DMC-Auditoria/3.0 (SimBrowser)', '2026-03-20 11:35:08.261739-03');
INSERT INTO public.tb_auditoria VALUES (490, 'criar', 'usuario', 62, 'audit_op2', 1, 'admin', 'super_admin', '[]', '{"nivel_acesso": "operador"}', '::1', 'DMC-Auditoria/3.0 (SimBrowser)', '2026-03-20 11:35:08.526398-03');
INSERT INTO public.tb_auditoria VALUES (491, 'criar', 'conexao', 61, '', 1, 'admin', 'super_admin', '[]', '[]', '::1', 'DMC-Auditoria/3.0 (SimBrowser)', '2026-03-20 11:35:10.255658-03');
INSERT INTO public.tb_auditoria VALUES (492, 'criar', 'conexao', 62, '', 1, 'admin', 'super_admin', '[]', '[]', '::1', 'DMC-Auditoria/3.0 (SimBrowser)', '2026-03-20 11:35:11.0472-03');
INSERT INTO public.tb_auditoria VALUES (493, 'login', 'sessao', 57, 'audit_admin1', 57, 'audit_admin1', 'admin', '[]', '[]', '::1', 'DMC-Auditoria/3.0 (SimBrowser)', '2026-03-20 11:35:16.276556-03');
INSERT INTO public.tb_auditoria VALUES (494, 'login', 'sessao', 58, 'audit_admin2', 58, 'audit_admin2', 'admin', '[]', '[]', '::1', 'DMC-Auditoria/3.0 (SimBrowser)', '2026-03-20 11:35:18.35252-03');
INSERT INTO public.tb_auditoria VALUES (495, 'login', 'sessao', 59, 'audit_dev1', 59, 'audit_dev1', 'desenvolvedor', '[]', '[]', '::1', 'DMC-Auditoria/3.0 (SimBrowser)', '2026-03-20 11:35:19.090434-03');
INSERT INTO public.tb_auditoria VALUES (496, 'criar', 'conexao', 63, '', 59, 'audit_dev1', 'desenvolvedor', '[]', '[]', '::1', 'DMC-Auditoria/3.0 (SimBrowser)', '2026-03-20 11:35:21.283808-03');
INSERT INTO public.tb_auditoria VALUES (497, 'login', 'sessao', 60, 'audit_dev2', 60, 'audit_dev2', 'desenvolvedor', '[]', '[]', '::1', 'DMC-Auditoria/3.0 (SimBrowser)', '2026-03-20 11:35:21.507969-03');
INSERT INTO public.tb_auditoria VALUES (498, 'login', 'sessao', 61, 'audit_op1', 61, 'audit_op1', 'operador', '[]', '[]', '::1', 'DMC-Auditoria/3.0 (SimBrowser)', '2026-03-20 11:35:22.769222-03');
INSERT INTO public.tb_auditoria VALUES (499, 'login', 'sessao', 62, 'audit_op2', 62, 'audit_op2', 'operador', '[]', '[]', '::1', 'DMC-Auditoria/3.0 (SimBrowser)', '2026-03-20 11:35:25.871079-03');
INSERT INTO public.tb_auditoria VALUES (500, 'excluir', 'conexao', 61, NULL, 1, 'admin', 'super_admin', '[]', '[]', '::1', 'DMC-Auditoria/3.0 (SimBrowser)', '2026-03-20 11:35:30.626824-03');
INSERT INTO public.tb_auditoria VALUES (501, 'excluir', 'conexao', 62, NULL, 1, 'admin', 'super_admin', '[]', '[]', '::1', 'DMC-Auditoria/3.0 (SimBrowser)', '2026-03-20 11:35:31.184958-03');
INSERT INTO public.tb_auditoria VALUES (502, 'excluir', 'conexao', 63, NULL, 1, 'admin', 'super_admin', '[]', '[]', '::1', 'DMC-Auditoria/3.0 (SimBrowser)', '2026-03-20 11:35:31.669817-03');
INSERT INTO public.tb_auditoria VALUES (503, 'excluir', 'usuario', 57, 'audit_admin1', 1, 'admin', 'super_admin', '[]', '[]', '::1', 'DMC-Auditoria/3.0 (SimBrowser)', '2026-03-20 11:35:31.812315-03');
INSERT INTO public.tb_auditoria VALUES (504, 'excluir', 'usuario', 58, 'audit_admin2', 1, 'admin', 'super_admin', '[]', '[]', '::1', 'DMC-Auditoria/3.0 (SimBrowser)', '2026-03-20 11:35:31.973906-03');
INSERT INTO public.tb_auditoria VALUES (505, 'excluir', 'usuario', 59, 'audit_dev1', 1, 'admin', 'super_admin', '[]', '[]', '::1', 'DMC-Auditoria/3.0 (SimBrowser)', '2026-03-20 11:35:32.148564-03');
INSERT INTO public.tb_auditoria VALUES (506, 'excluir', 'usuario', 60, 'audit_dev2', 1, 'admin', 'super_admin', '[]', '[]', '::1', 'DMC-Auditoria/3.0 (SimBrowser)', '2026-03-20 11:35:32.315584-03');
INSERT INTO public.tb_auditoria VALUES (507, 'excluir', 'usuario', 61, 'audit_op1', 1, 'admin', 'super_admin', '[]', '[]', '::1', 'DMC-Auditoria/3.0 (SimBrowser)', '2026-03-20 11:35:32.466043-03');
INSERT INTO public.tb_auditoria VALUES (508, 'excluir', 'usuario', 62, 'audit_op2', 1, 'admin', 'super_admin', '[]', '[]', '::1', 'DMC-Auditoria/3.0 (SimBrowser)', '2026-03-20 11:35:32.624151-03');
INSERT INTO public.tb_auditoria VALUES (509, 'excluir', 'empresa', 26, 'Secretaria de TI', 1, 'admin', 'super_admin', '[]', '[]', '::1', 'DMC-Auditoria/3.0 (SimBrowser)', '2026-03-20 11:35:34.330547-03');
INSERT INTO public.tb_auditoria VALUES (510, 'excluir', 'empresa', 27, 'Secretaria de Saúde', 1, 'admin', 'super_admin', '[]', '[]', '::1', 'DMC-Auditoria/3.0 (SimBrowser)', '2026-03-20 11:35:34.476478-03');
INSERT INTO public.tb_auditoria VALUES (511, 'excluir', 'empresa', 28, 'Secretaria de Educação', 1, 'admin', 'super_admin', '[]', '[]', '::1', 'DMC-Auditoria/3.0 (SimBrowser)', '2026-03-20 11:35:34.641148-03');
INSERT INTO public.tb_auditoria VALUES (512, 'excluir', 'empresa', 29, 'Secretaria de Finanças', 1, 'admin', 'super_admin', '[]', '[]', '::1', 'DMC-Auditoria/3.0 (SimBrowser)', '2026-03-20 11:35:34.783614-03');
INSERT INTO public.tb_auditoria VALUES (513, 'excluir', 'empresa', 30, 'Secretaria de Obras', 1, 'admin', 'super_admin', '[]', '[]', '::1', 'DMC-Auditoria/3.0 (SimBrowser)', '2026-03-20 11:35:34.942339-03');
INSERT INTO public.tb_auditoria VALUES (514, 'logout', 'sessao', NULL, NULL, 23, 'renan', 'operador', '[]', '[]', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-20 11:36:37.40592-03');
INSERT INTO public.tb_auditoria VALUES (515, 'login', 'sessao', 23, 'renan', 23, 'renan', 'operador', '[]', '[]', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-20 11:36:40.432468-03');
INSERT INTO public.tb_auditoria VALUES (516, 'logout', 'sessao', NULL, NULL, 23, 'renan', 'operador', '[]', '[]', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-20 11:36:52.542336-03');
INSERT INTO public.tb_auditoria VALUES (517, 'login', 'sessao', 1, 'admin', 1, 'admin', 'super_admin', '[]', '[]', '::1', 'DMC-Auditoria/3.0 (SimBrowser)', '2026-03-20 11:37:07.9854-03');
INSERT INTO public.tb_auditoria VALUES (518, 'criar', 'empresa', 31, 'Secretaria de TI', 1, 'admin', 'super_admin', '[]', '[]', '::1', 'DMC-Auditoria/3.0 (SimBrowser)', '2026-03-20 11:37:08.388549-03');
INSERT INTO public.tb_auditoria VALUES (519, 'criar', 'empresa', 32, 'Secretaria de Saúde', 1, 'admin', 'super_admin', '[]', '[]', '::1', 'DMC-Auditoria/3.0 (SimBrowser)', '2026-03-20 11:37:08.55339-03');
INSERT INTO public.tb_auditoria VALUES (520, 'criar', 'empresa', 33, 'Secretaria de Educação', 1, 'admin', 'super_admin', '[]', '[]', '::1', 'DMC-Auditoria/3.0 (SimBrowser)', '2026-03-20 11:37:08.715339-03');
INSERT INTO public.tb_auditoria VALUES (521, 'criar', 'empresa', 34, 'Secretaria de Finanças', 1, 'admin', 'super_admin', '[]', '[]', '::1', 'DMC-Auditoria/3.0 (SimBrowser)', '2026-03-20 11:37:08.874934-03');
INSERT INTO public.tb_auditoria VALUES (522, 'criar', 'empresa', 35, 'Secretaria de Obras', 1, 'admin', 'super_admin', '[]', '[]', '::1', 'DMC-Auditoria/3.0 (SimBrowser)', '2026-03-20 11:37:09.04426-03');
INSERT INTO public.tb_auditoria VALUES (523, 'criar', 'usuario', 63, 'audit_admin1', 1, 'admin', 'super_admin', '[]', '{"nivel_acesso": "admin"}', '::1', 'DMC-Auditoria/3.0 (SimBrowser)', '2026-03-20 11:37:11.192276-03');
INSERT INTO public.tb_auditoria VALUES (524, 'criar', 'usuario', 64, 'audit_admin2', 1, 'admin', 'super_admin', '[]', '{"nivel_acesso": "admin"}', '::1', 'DMC-Auditoria/3.0 (SimBrowser)', '2026-03-20 11:37:11.46152-03');
INSERT INTO public.tb_auditoria VALUES (525, 'criar', 'usuario', 65, 'audit_dev1', 1, 'admin', 'super_admin', '[]', '{"nivel_acesso": "desenvolvedor"}', '::1', 'DMC-Auditoria/3.0 (SimBrowser)', '2026-03-20 11:37:11.744667-03');
INSERT INTO public.tb_auditoria VALUES (526, 'criar', 'usuario', 66, 'audit_dev2', 1, 'admin', 'super_admin', '[]', '{"nivel_acesso": "desenvolvedor"}', '::1', 'DMC-Auditoria/3.0 (SimBrowser)', '2026-03-20 11:37:12.015894-03');
INSERT INTO public.tb_auditoria VALUES (527, 'criar', 'usuario', 67, 'audit_op1', 1, 'admin', 'super_admin', '[]', '{"nivel_acesso": "operador"}', '::1', 'DMC-Auditoria/3.0 (SimBrowser)', '2026-03-20 11:37:12.403528-03');
INSERT INTO public.tb_auditoria VALUES (528, 'criar', 'usuario', 68, 'audit_op2', 1, 'admin', 'super_admin', '[]', '{"nivel_acesso": "operador"}', '::1', 'DMC-Auditoria/3.0 (SimBrowser)', '2026-03-20 11:37:12.829382-03');
INSERT INTO public.tb_auditoria VALUES (529, 'criar', 'conexao', 64, '', 1, 'admin', 'super_admin', '[]', '[]', '::1', 'DMC-Auditoria/3.0 (SimBrowser)', '2026-03-20 11:37:14.772702-03');
INSERT INTO public.tb_auditoria VALUES (530, 'criar', 'conexao', 65, '', 1, 'admin', 'super_admin', '[]', '[]', '::1', 'DMC-Auditoria/3.0 (SimBrowser)', '2026-03-20 11:37:15.825216-03');
INSERT INTO public.tb_auditoria VALUES (531, 'login', 'sessao', 63, 'audit_admin1', 63, 'audit_admin1', 'admin', '[]', '[]', '::1', 'DMC-Auditoria/3.0 (SimBrowser)', '2026-03-20 11:37:21.683671-03');
INSERT INTO public.tb_auditoria VALUES (532, 'login', 'sessao', 64, 'audit_admin2', 64, 'audit_admin2', 'admin', '[]', '[]', '::1', 'DMC-Auditoria/3.0 (SimBrowser)', '2026-03-20 11:37:25.020858-03');
INSERT INTO public.tb_auditoria VALUES (533, 'login', 'sessao', 65, 'audit_dev1', 65, 'audit_dev1', 'desenvolvedor', '[]', '[]', '::1', 'DMC-Auditoria/3.0 (SimBrowser)', '2026-03-20 11:37:25.866704-03');
INSERT INTO public.tb_auditoria VALUES (534, 'criar', 'conexao', 66, '', 65, 'audit_dev1', 'desenvolvedor', '[]', '[]', '::1', 'DMC-Auditoria/3.0 (SimBrowser)', '2026-03-20 11:37:28.04864-03');
INSERT INTO public.tb_auditoria VALUES (535, 'login', 'sessao', 66, 'audit_dev2', 66, 'audit_dev2', 'desenvolvedor', '[]', '[]', '::1', 'DMC-Auditoria/3.0 (SimBrowser)', '2026-03-20 11:37:28.29676-03');
INSERT INTO public.tb_auditoria VALUES (536, 'login', 'sessao', 67, 'audit_op1', 67, 'audit_op1', 'operador', '[]', '[]', '::1', 'DMC-Auditoria/3.0 (SimBrowser)', '2026-03-20 11:37:29.666007-03');
INSERT INTO public.tb_auditoria VALUES (537, 'login', 'sessao', 68, 'audit_op2', 68, 'audit_op2', 'operador', '[]', '[]', '::1', 'DMC-Auditoria/3.0 (SimBrowser)', '2026-03-20 11:37:32.796639-03');
INSERT INTO public.tb_auditoria VALUES (538, 'excluir', 'conexao', 64, NULL, 1, 'admin', 'super_admin', '[]', '[]', '::1', 'DMC-Auditoria/3.0 (SimBrowser)', '2026-03-20 11:37:37.535394-03');
INSERT INTO public.tb_auditoria VALUES (539, 'excluir', 'conexao', 65, NULL, 1, 'admin', 'super_admin', '[]', '[]', '::1', 'DMC-Auditoria/3.0 (SimBrowser)', '2026-03-20 11:37:38.012847-03');
INSERT INTO public.tb_auditoria VALUES (540, 'excluir', 'conexao', 66, NULL, 1, 'admin', 'super_admin', '[]', '[]', '::1', 'DMC-Auditoria/3.0 (SimBrowser)', '2026-03-20 11:37:38.494081-03');
INSERT INTO public.tb_auditoria VALUES (541, 'excluir', 'usuario', 63, 'audit_admin1', 1, 'admin', 'super_admin', '[]', '[]', '::1', 'DMC-Auditoria/3.0 (SimBrowser)', '2026-03-20 11:37:38.653868-03');
INSERT INTO public.tb_auditoria VALUES (542, 'excluir', 'usuario', 64, 'audit_admin2', 1, 'admin', 'super_admin', '[]', '[]', '::1', 'DMC-Auditoria/3.0 (SimBrowser)', '2026-03-20 11:37:38.825982-03');
INSERT INTO public.tb_auditoria VALUES (543, 'excluir', 'usuario', 65, 'audit_dev1', 1, 'admin', 'super_admin', '[]', '[]', '::1', 'DMC-Auditoria/3.0 (SimBrowser)', '2026-03-20 11:37:39.00105-03');
INSERT INTO public.tb_auditoria VALUES (544, 'excluir', 'usuario', 66, 'audit_dev2', 1, 'admin', 'super_admin', '[]', '[]', '::1', 'DMC-Auditoria/3.0 (SimBrowser)', '2026-03-20 11:37:39.195143-03');
INSERT INTO public.tb_auditoria VALUES (545, 'excluir', 'usuario', 67, 'audit_op1', 1, 'admin', 'super_admin', '[]', '[]', '::1', 'DMC-Auditoria/3.0 (SimBrowser)', '2026-03-20 11:37:39.459852-03');
INSERT INTO public.tb_auditoria VALUES (546, 'excluir', 'usuario', 68, 'audit_op2', 1, 'admin', 'super_admin', '[]', '[]', '::1', 'DMC-Auditoria/3.0 (SimBrowser)', '2026-03-20 11:37:39.720688-03');
INSERT INTO public.tb_auditoria VALUES (547, 'excluir', 'empresa', 31, 'Secretaria de TI', 1, 'admin', 'super_admin', '[]', '[]', '::1', 'DMC-Auditoria/3.0 (SimBrowser)', '2026-03-20 11:37:41.77927-03');
INSERT INTO public.tb_auditoria VALUES (548, 'excluir', 'empresa', 32, 'Secretaria de Saúde', 1, 'admin', 'super_admin', '[]', '[]', '::1', 'DMC-Auditoria/3.0 (SimBrowser)', '2026-03-20 11:37:41.939422-03');
INSERT INTO public.tb_auditoria VALUES (549, 'excluir', 'empresa', 33, 'Secretaria de Educação', 1, 'admin', 'super_admin', '[]', '[]', '::1', 'DMC-Auditoria/3.0 (SimBrowser)', '2026-03-20 11:37:42.107227-03');
INSERT INTO public.tb_auditoria VALUES (550, 'excluir', 'empresa', 34, 'Secretaria de Finanças', 1, 'admin', 'super_admin', '[]', '[]', '::1', 'DMC-Auditoria/3.0 (SimBrowser)', '2026-03-20 11:37:42.258107-03');
INSERT INTO public.tb_auditoria VALUES (551, 'excluir', 'empresa', 35, 'Secretaria de Obras', 1, 'admin', 'super_admin', '[]', '[]', '::1', 'DMC-Auditoria/3.0 (SimBrowser)', '2026-03-20 11:37:42.431085-03');
INSERT INTO public.tb_auditoria VALUES (552, 'login', 'sessao', 1, 'admin', 1, 'admin', 'super_admin', '[]', '[]', '::1', 'DMC-BrowserTest/2.0 (Simulated Browser)', '2026-03-20 11:37:58.762405-03');
INSERT INTO public.tb_auditoria VALUES (553, 'criar', 'empresa', 36, 'DMC Tecnologia', 1, 'admin', 'super_admin', '[]', '[]', '::1', 'DMC-BrowserTest/2.0 (Simulated Browser)', '2026-03-20 11:37:59.125554-03');
INSERT INTO public.tb_auditoria VALUES (554, 'criar', 'empresa', 37, 'Alpha Corp', 1, 'admin', 'super_admin', '[]', '[]', '::1', 'DMC-BrowserTest/2.0 (Simulated Browser)', '2026-03-20 11:37:59.312301-03');
INSERT INTO public.tb_auditoria VALUES (555, 'criar', 'empresa', 38, 'Beta Sistemas', 1, 'admin', 'super_admin', '[]', '[]', '::1', 'DMC-BrowserTest/2.0 (Simulated Browser)', '2026-03-20 11:37:59.496286-03');
INSERT INTO public.tb_auditoria VALUES (556, 'criar', 'usuario', 69, 'admin_browser_test', 1, 'admin', 'super_admin', '[]', '{"nivel_acesso": "admin"}', '::1', 'DMC-BrowserTest/2.0 (Simulated Browser)', '2026-03-20 11:38:01.00067-03');
INSERT INTO public.tb_auditoria VALUES (557, 'criar', 'usuario', 70, 'dev_browser_test', 1, 'admin', 'super_admin', '[]', '{"nivel_acesso": "desenvolvedor"}', '::1', 'DMC-BrowserTest/2.0 (Simulated Browser)', '2026-03-20 11:38:01.240175-03');
INSERT INTO public.tb_auditoria VALUES (558, 'criar', 'usuario', 71, 'op_browser_test', 1, 'admin', 'super_admin', '[]', '{"nivel_acesso": "operador"}', '::1', 'DMC-BrowserTest/2.0 (Simulated Browser)', '2026-03-20 11:38:01.453601-03');
INSERT INTO public.tb_auditoria VALUES (559, 'criar', 'conexao', 67, '', 1, 'admin', 'super_admin', '[]', '[]', '::1', 'DMC-BrowserTest/2.0 (Simulated Browser)', '2026-03-20 11:38:07.075006-03');
INSERT INTO public.tb_auditoria VALUES (560, 'login', 'sessao', 69, 'admin_browser_test', 69, 'admin_browser_test', 'admin', '[]', '[]', '::1', 'DMC-BrowserTest/2.0 (Simulated Browser)', '2026-03-20 11:38:08.195496-03');
INSERT INTO public.tb_auditoria VALUES (561, 'criar', 'conexao', 68, '', 69, 'admin_browser_test', 'admin', '[]', '[]', '::1', 'DMC-BrowserTest/2.0 (Simulated Browser)', '2026-03-20 11:38:09.972055-03');
INSERT INTO public.tb_auditoria VALUES (562, 'login', 'sessao', 70, 'dev_browser_test', 70, 'dev_browser_test', 'desenvolvedor', '[]', '[]', '::1', 'DMC-BrowserTest/2.0 (Simulated Browser)', '2026-03-20 11:38:10.341539-03');
INSERT INTO public.tb_auditoria VALUES (563, 'criar', 'conexao', 69, '', 70, 'dev_browser_test', 'desenvolvedor', '[]', '[]', '::1', 'DMC-BrowserTest/2.0 (Simulated Browser)', '2026-03-20 11:38:11.721206-03');
INSERT INTO public.tb_auditoria VALUES (564, 'login', 'sessao', 71, 'op_browser_test', 71, 'op_browser_test', 'operador', '[]', '[]', '::1', 'DMC-BrowserTest/2.0 (Simulated Browser)', '2026-03-20 11:38:12.34968-03');
INSERT INTO public.tb_auditoria VALUES (565, 'excluir', 'conexao', 67, NULL, 1, 'admin', 'super_admin', '[]', '[]', '::1', 'DMC-BrowserTest/2.0 (Simulated Browser)', '2026-03-20 11:38:17.080668-03');
INSERT INTO public.tb_auditoria VALUES (566, 'excluir', 'conexao', 68, NULL, 1, 'admin', 'super_admin', '[]', '[]', '::1', 'DMC-BrowserTest/2.0 (Simulated Browser)', '2026-03-20 11:38:17.561082-03');
INSERT INTO public.tb_auditoria VALUES (567, 'excluir', 'conexao', 69, NULL, 1, 'admin', 'super_admin', '[]', '[]', '::1', 'DMC-BrowserTest/2.0 (Simulated Browser)', '2026-03-20 11:38:18.085604-03');
INSERT INTO public.tb_auditoria VALUES (568, 'excluir', 'usuario', 69, 'admin_browser_test', 1, 'admin', 'super_admin', '[]', '[]', '::1', 'DMC-BrowserTest/2.0 (Simulated Browser)', '2026-03-20 11:38:18.239268-03');
INSERT INTO public.tb_auditoria VALUES (569, 'excluir', 'usuario', 70, 'dev_browser_test', 1, 'admin', 'super_admin', '[]', '[]', '::1', 'DMC-BrowserTest/2.0 (Simulated Browser)', '2026-03-20 11:38:18.405048-03');
INSERT INTO public.tb_auditoria VALUES (570, 'excluir', 'usuario', 71, 'op_browser_test', 1, 'admin', 'super_admin', '[]', '[]', '::1', 'DMC-BrowserTest/2.0 (Simulated Browser)', '2026-03-20 11:38:18.584677-03');
INSERT INTO public.tb_auditoria VALUES (571, 'excluir', 'empresa', 36, 'DMC Tecnologia', 1, 'admin', 'super_admin', '[]', '[]', '::1', 'DMC-BrowserTest/2.0 (Simulated Browser)', '2026-03-20 11:38:19.567149-03');
INSERT INTO public.tb_auditoria VALUES (572, 'excluir', 'empresa', 37, 'Alpha Corp', 1, 'admin', 'super_admin', '[]', '[]', '::1', 'DMC-BrowserTest/2.0 (Simulated Browser)', '2026-03-20 11:38:19.706098-03');
INSERT INTO public.tb_auditoria VALUES (573, 'excluir', 'empresa', 38, 'Beta Sistemas', 1, 'admin', 'super_admin', '[]', '[]', '::1', 'DMC-BrowserTest/2.0 (Simulated Browser)', '2026-03-20 11:38:19.84258-03');
INSERT INTO public.tb_auditoria VALUES (574, 'login', 'sessao', 1, 'admin', 1, 'admin', 'super_admin', '[]', '[]', '::1', '', '2026-03-20 11:38:22.407152-03');
INSERT INTO public.tb_auditoria VALUES (575, 'criar', 'empresa', 39, 'Empresa Teste E2E', 1, 'admin', 'super_admin', '[]', '[]', '::1', '', '2026-03-20 11:38:23.776883-03');
INSERT INTO public.tb_auditoria VALUES (576, 'criar', 'usuario', 72, 'testuser_e2e', 1, 'admin', 'super_admin', '[]', '{"nivel_acesso": "operador"}', '::1', '', '2026-03-20 11:38:24.371273-03');
INSERT INTO public.tb_auditoria VALUES (577, 'login', 'sessao', 72, 'testuser_e2e', 72, 'testuser_e2e', 'operador', '[]', '[]', '::1', '', '2026-03-20 11:38:24.762297-03');
INSERT INTO public.tb_auditoria VALUES (578, 'logout', 'sessao', NULL, NULL, 72, 'testuser_e2e', 'operador', '[]', '[]', '::1', '', '2026-03-20 11:38:24.918392-03');
INSERT INTO public.tb_auditoria VALUES (579, 'login', 'sessao', 72, 'testuser_e2e', 72, 'testuser_e2e', 'operador', '[]', '[]', '::1', '', '2026-03-20 11:38:25.538272-03');
INSERT INTO public.tb_auditoria VALUES (580, 'logout', 'sessao', NULL, NULL, 72, 'testuser_e2e', 'operador', '[]', '[]', '::1', '', '2026-03-20 11:38:25.675905-03');
INSERT INTO public.tb_auditoria VALUES (581, 'criar', 'conexao', 70, '', 1, 'admin', 'super_admin', '[]', '[]', '::1', '', '2026-03-20 11:38:26.272921-03');
INSERT INTO public.tb_auditoria VALUES (582, 'excluir', 'conexao', 70, NULL, 1, 'admin', 'super_admin', '[]', '[]', '::1', '', '2026-03-20 11:38:31.099591-03');
INSERT INTO public.tb_auditoria VALUES (583, 'excluir', 'usuario', 72, 'testuser_e2e', 1, 'admin', 'super_admin', '[]', '[]', '::1', '', '2026-03-20 11:38:31.23874-03');
INSERT INTO public.tb_auditoria VALUES (584, 'excluir', 'empresa', 39, 'Empresa Teste E2E', 1, 'admin', 'super_admin', '[]', '[]', '::1', '', '2026-03-20 11:38:31.549492-03');
INSERT INTO public.tb_auditoria VALUES (585, 'login', 'sessao', 1, 'admin', 1, 'admin', 'super_admin', '[]', '[]', '::1', '', '2026-03-20 11:38:34.849283-03');
INSERT INTO public.tb_auditoria VALUES (586, 'login', 'sessao', 1, 'admin', 1, 'admin', 'super_admin', '[]', '[]', '::1', '', '2026-03-20 11:38:39.882203-03');
INSERT INTO public.tb_auditoria VALUES (587, 'login', 'sessao', 1, 'admin', 1, 'admin', 'super_admin', '[]', '[]', '::1', '', '2026-03-20 11:38:44.674288-03');
INSERT INTO public.tb_auditoria VALUES (588, 'login', 'sessao', 73, 'test_dev', 73, 'test_dev', 'desenvolvedor', '[]', '[]', '::1', '', '2026-03-20 11:38:44.832696-03');
INSERT INTO public.tb_auditoria VALUES (589, 'login', 'sessao', 74, 'test_op', 74, 'test_op', 'operador', '[]', '[]', '::1', '', '2026-03-20 11:38:44.988947-03');
INSERT INTO public.tb_auditoria VALUES (590, 'login', 'sessao', 1, 'admin', 1, 'admin', 'super_admin', '[]', '[]', '::1', '', '2026-03-20 11:38:48.773281-03');
INSERT INTO public.tb_auditoria VALUES (591, 'criar', 'pipeline', 26, 'Pipeline Teste E2E', 1, 'admin', 'super_admin', '[]', '[]', '::1', '', '2026-03-20 11:38:49.020103-03');
INSERT INTO public.tb_auditoria VALUES (592, 'excluir', 'pipeline', 26, NULL, 1, 'admin', 'super_admin', '[]', '[]', '::1', '', '2026-03-20 11:38:49.220388-03');
INSERT INTO public.tb_auditoria VALUES (593, 'criar', 'workflow', 18, 'Workflow Teste E2E', 1, 'admin', 'super_admin', '[]', '[]', '::1', '', '2026-03-20 11:38:49.466941-03');
INSERT INTO public.tb_auditoria VALUES (594, 'excluir', 'workflow', 18, NULL, 1, 'admin', 'super_admin', '[]', '[]', '::1', '', '2026-03-20 11:38:49.806519-03');
INSERT INTO public.tb_auditoria VALUES (595, 'login', 'sessao', 1, 'admin', 1, 'admin', 'super_admin', '[]', '[]', '::1', '', '2026-03-20 11:38:51.561792-03');
INSERT INTO public.tb_auditoria VALUES (596, 'editar', 'configuracao', NULL, 'Grupo: geral', 1, 'admin', 'super_admin', '{"app_nome": "DMC DataLoad"}', '{"app_nome": "DMC DataLoad"}', '::1', '', '2026-03-20 11:38:52.020663-03');
INSERT INTO public.tb_auditoria VALUES (597, 'criar', 'empresa', 40, 'Agência de tecnologia e inovação', 1, 'admin', 'super_admin', '[]', '[]', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-20 11:41:13.615046-03');
INSERT INTO public.tb_auditoria VALUES (598, 'editar', 'conexao', 15, '', 1, 'admin', 'super_admin', '[]', '[]', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-20 11:42:04.352062-03');
INSERT INTO public.tb_auditoria VALUES (599, 'editar', 'conexao', 15, '', 1, 'admin', 'super_admin', '[]', '[]', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-20 11:43:02.462942-03');
INSERT INTO public.tb_auditoria VALUES (600, 'editar', 'conexao', 15, '', 1, 'admin', 'super_admin', '[]', '[]', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-20 11:43:26.287851-03');
INSERT INTO public.tb_auditoria VALUES (601, 'login', 'sessao', 1, 'admin', 1, 'admin', 'super_admin', '[]', '[]', '::1', 'DMC-BrowserTest/2.0 (Simulated Browser)', '2026-03-20 11:52:55.041125-03');
INSERT INTO public.tb_auditoria VALUES (602, 'criar', 'empresa', 41, 'DMC Tecnologia', 1, 'admin', 'super_admin', '[]', '[]', '::1', 'DMC-BrowserTest/2.0 (Simulated Browser)', '2026-03-20 11:52:55.38286-03');
INSERT INTO public.tb_auditoria VALUES (603, 'criar', 'empresa', 42, 'Alpha Corp', 1, 'admin', 'super_admin', '[]', '[]', '::1', 'DMC-BrowserTest/2.0 (Simulated Browser)', '2026-03-20 11:52:55.572168-03');
INSERT INTO public.tb_auditoria VALUES (604, 'criar', 'empresa', 43, 'Beta Sistemas', 1, 'admin', 'super_admin', '[]', '[]', '::1', 'DMC-BrowserTest/2.0 (Simulated Browser)', '2026-03-20 11:52:55.754307-03');
INSERT INTO public.tb_auditoria VALUES (605, 'criar', 'usuario', 75, 'admin_browser_test', 1, 'admin', 'super_admin', '[]', '{"nivel_acesso": "admin"}', '::1', 'DMC-BrowserTest/2.0 (Simulated Browser)', '2026-03-20 11:52:57.497218-03');
INSERT INTO public.tb_auditoria VALUES (606, 'criar', 'usuario', 76, 'dev_browser_test', 1, 'admin', 'super_admin', '[]', '{"nivel_acesso": "desenvolvedor"}', '::1', 'DMC-BrowserTest/2.0 (Simulated Browser)', '2026-03-20 11:52:57.784284-03');
INSERT INTO public.tb_auditoria VALUES (607, 'criar', 'usuario', 77, 'op_browser_test', 1, 'admin', 'super_admin', '[]', '{"nivel_acesso": "operador"}', '::1', 'DMC-BrowserTest/2.0 (Simulated Browser)', '2026-03-20 11:52:58.028498-03');
INSERT INTO public.tb_auditoria VALUES (608, 'criar', 'conexao', 71, '', 1, 'admin', 'super_admin', '[]', '[]', '::1', 'DMC-BrowserTest/2.0 (Simulated Browser)', '2026-03-20 11:53:06.88218-03');
INSERT INTO public.tb_auditoria VALUES (609, 'login', 'sessao', 75, 'admin_browser_test', 75, 'admin_browser_test', 'admin', '[]', '[]', '::1', 'DMC-BrowserTest/2.0 (Simulated Browser)', '2026-03-20 11:53:08.363918-03');
INSERT INTO public.tb_auditoria VALUES (610, 'criar', 'conexao', 72, '', 75, 'admin_browser_test', 'admin', '[]', '[]', '::1', 'DMC-BrowserTest/2.0 (Simulated Browser)', '2026-03-20 11:53:11.350603-03');
INSERT INTO public.tb_auditoria VALUES (611, 'login', 'sessao', 76, 'dev_browser_test', 76, 'dev_browser_test', 'desenvolvedor', '[]', '[]', '::1', 'DMC-BrowserTest/2.0 (Simulated Browser)', '2026-03-20 11:53:11.74473-03');
INSERT INTO public.tb_auditoria VALUES (612, 'criar', 'conexao', 73, '', 76, 'dev_browser_test', 'desenvolvedor', '[]', '[]', '::1', 'DMC-BrowserTest/2.0 (Simulated Browser)', '2026-03-20 11:53:13.463942-03');
INSERT INTO public.tb_auditoria VALUES (613, 'login', 'sessao', 77, 'op_browser_test', 77, 'op_browser_test', 'operador', '[]', '[]', '::1', 'DMC-BrowserTest/2.0 (Simulated Browser)', '2026-03-20 11:53:14.175737-03');
INSERT INTO public.tb_auditoria VALUES (614, 'excluir', 'conexao', 71, NULL, 1, 'admin', 'super_admin', '[]', '[]', '::1', 'DMC-BrowserTest/2.0 (Simulated Browser)', '2026-03-20 11:53:19.896345-03');
INSERT INTO public.tb_auditoria VALUES (615, 'excluir', 'conexao', 72, NULL, 1, 'admin', 'super_admin', '[]', '[]', '::1', 'DMC-BrowserTest/2.0 (Simulated Browser)', '2026-03-20 11:53:20.557508-03');
INSERT INTO public.tb_auditoria VALUES (616, 'excluir', 'conexao', 73, NULL, 1, 'admin', 'super_admin', '[]', '[]', '::1', 'DMC-BrowserTest/2.0 (Simulated Browser)', '2026-03-20 11:53:21.346078-03');
INSERT INTO public.tb_auditoria VALUES (617, 'excluir', 'usuario', 75, 'admin_browser_test', 1, 'admin', 'super_admin', '[]', '[]', '::1', 'DMC-BrowserTest/2.0 (Simulated Browser)', '2026-03-20 11:53:21.526681-03');
INSERT INTO public.tb_auditoria VALUES (618, 'excluir', 'usuario', 76, 'dev_browser_test', 1, 'admin', 'super_admin', '[]', '[]', '::1', 'DMC-BrowserTest/2.0 (Simulated Browser)', '2026-03-20 11:53:21.715522-03');
INSERT INTO public.tb_auditoria VALUES (619, 'excluir', 'usuario', 77, 'op_browser_test', 1, 'admin', 'super_admin', '[]', '[]', '::1', 'DMC-BrowserTest/2.0 (Simulated Browser)', '2026-03-20 11:53:21.905712-03');
INSERT INTO public.tb_auditoria VALUES (620, 'excluir', 'empresa', 41, 'DMC Tecnologia', 1, 'admin', 'super_admin', '[]', '[]', '::1', 'DMC-BrowserTest/2.0 (Simulated Browser)', '2026-03-20 11:53:23.365152-03');
INSERT INTO public.tb_auditoria VALUES (621, 'excluir', 'empresa', 42, 'Alpha Corp', 1, 'admin', 'super_admin', '[]', '[]', '::1', 'DMC-BrowserTest/2.0 (Simulated Browser)', '2026-03-20 11:53:23.557501-03');
INSERT INTO public.tb_auditoria VALUES (622, 'excluir', 'empresa', 43, 'Beta Sistemas', 1, 'admin', 'super_admin', '[]', '[]', '::1', 'DMC-BrowserTest/2.0 (Simulated Browser)', '2026-03-20 11:53:23.719805-03');
INSERT INTO public.tb_auditoria VALUES (623, 'login', 'sessao', 1, 'admin', 1, 'admin', 'super_admin', '[]', '[]', '::1', 'DMC-Auditoria/3.0 (SimBrowser)', '2026-03-20 11:53:29.746534-03');
INSERT INTO public.tb_auditoria VALUES (624, 'criar', 'empresa', 44, 'Secretaria de TI', 1, 'admin', 'super_admin', '[]', '[]', '::1', 'DMC-Auditoria/3.0 (SimBrowser)', '2026-03-20 11:53:30.109124-03');
INSERT INTO public.tb_auditoria VALUES (625, 'criar', 'empresa', 45, 'Secretaria de Saúde', 1, 'admin', 'super_admin', '[]', '[]', '::1', 'DMC-Auditoria/3.0 (SimBrowser)', '2026-03-20 11:53:30.239996-03');
INSERT INTO public.tb_auditoria VALUES (626, 'criar', 'empresa', 46, 'Secretaria de Educação', 1, 'admin', 'super_admin', '[]', '[]', '::1', 'DMC-Auditoria/3.0 (SimBrowser)', '2026-03-20 11:53:30.40933-03');
INSERT INTO public.tb_auditoria VALUES (627, 'criar', 'empresa', 47, 'Secretaria de Finanças', 1, 'admin', 'super_admin', '[]', '[]', '::1', 'DMC-Auditoria/3.0 (SimBrowser)', '2026-03-20 11:53:30.58179-03');
INSERT INTO public.tb_auditoria VALUES (628, 'criar', 'empresa', 48, 'Secretaria de Obras', 1, 'admin', 'super_admin', '[]', '[]', '::1', 'DMC-Auditoria/3.0 (SimBrowser)', '2026-03-20 11:53:30.760607-03');
INSERT INTO public.tb_auditoria VALUES (629, 'editar', 'conexao', 11, '', 1, 'admin', 'super_admin', '[]', '[]', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-20 11:53:31.240078-03');
INSERT INTO public.tb_auditoria VALUES (630, 'criar', 'usuario', 78, 'audit_admin1', 1, 'admin', 'super_admin', '[]', '{"nivel_acesso": "admin"}', '::1', 'DMC-Auditoria/3.0 (SimBrowser)', '2026-03-20 11:53:33.113297-03');
INSERT INTO public.tb_auditoria VALUES (631, 'criar', 'usuario', 79, 'audit_admin2', 1, 'admin', 'super_admin', '[]', '{"nivel_acesso": "admin"}', '::1', 'DMC-Auditoria/3.0 (SimBrowser)', '2026-03-20 11:53:33.311318-03');
INSERT INTO public.tb_auditoria VALUES (632, 'criar', 'usuario', 80, 'audit_dev1', 1, 'admin', 'super_admin', '[]', '{"nivel_acesso": "desenvolvedor"}', '::1', 'DMC-Auditoria/3.0 (SimBrowser)', '2026-03-20 11:53:33.531367-03');
INSERT INTO public.tb_auditoria VALUES (633, 'criar', 'usuario', 81, 'audit_dev2', 1, 'admin', 'super_admin', '[]', '{"nivel_acesso": "desenvolvedor"}', '::1', 'DMC-Auditoria/3.0 (SimBrowser)', '2026-03-20 11:53:33.761595-03');
INSERT INTO public.tb_auditoria VALUES (634, 'criar', 'usuario', 82, 'audit_op1', 1, 'admin', 'super_admin', '[]', '{"nivel_acesso": "operador"}', '::1', 'DMC-Auditoria/3.0 (SimBrowser)', '2026-03-20 11:53:33.991472-03');
INSERT INTO public.tb_auditoria VALUES (635, 'criar', 'usuario', 83, 'audit_op2', 1, 'admin', 'super_admin', '[]', '{"nivel_acesso": "operador"}', '::1', 'DMC-Auditoria/3.0 (SimBrowser)', '2026-03-20 11:53:34.270615-03');
INSERT INTO public.tb_auditoria VALUES (636, 'criar', 'conexao', 74, '', 1, 'admin', 'super_admin', '[]', '[]', '::1', 'DMC-Auditoria/3.0 (SimBrowser)', '2026-03-20 11:53:35.729437-03');
INSERT INTO public.tb_auditoria VALUES (637, 'criar', 'conexao', 75, '', 1, 'admin', 'super_admin', '[]', '[]', '::1', 'DMC-Auditoria/3.0 (SimBrowser)', '2026-03-20 11:53:36.258223-03');
INSERT INTO public.tb_auditoria VALUES (638, 'login', 'sessao', 23, 'renan', 23, 'renan', 'operador', '[]', '[]', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-20 11:53:38.083678-03');
INSERT INTO public.tb_auditoria VALUES (639, 'login', 'sessao', 78, 'audit_admin1', 78, 'audit_admin1', 'admin', '[]', '[]', '::1', 'DMC-Auditoria/3.0 (SimBrowser)', '2026-03-20 11:53:40.344606-03');
INSERT INTO public.tb_auditoria VALUES (640, 'login', 'sessao', 79, 'audit_admin2', 79, 'audit_admin2', 'admin', '[]', '[]', '::1', 'DMC-Auditoria/3.0 (SimBrowser)', '2026-03-20 11:53:43.019482-03');
INSERT INTO public.tb_auditoria VALUES (641, 'login', 'sessao', 80, 'audit_dev1', 80, 'audit_dev1', 'desenvolvedor', '[]', '[]', '::1', 'DMC-Auditoria/3.0 (SimBrowser)', '2026-03-20 11:53:43.815806-03');
INSERT INTO public.tb_auditoria VALUES (642, 'criar', 'conexao', 76, '', 80, 'audit_dev1', 'desenvolvedor', '[]', '[]', '::1', 'DMC-Auditoria/3.0 (SimBrowser)', '2026-03-20 11:53:45.506581-03');
INSERT INTO public.tb_auditoria VALUES (643, 'login', 'sessao', 81, 'audit_dev2', 81, 'audit_dev2', 'desenvolvedor', '[]', '[]', '::1', 'DMC-Auditoria/3.0 (SimBrowser)', '2026-03-20 11:53:45.712203-03');
INSERT INTO public.tb_auditoria VALUES (644, 'login', 'sessao', 82, 'audit_op1', 82, 'audit_op1', 'operador', '[]', '[]', '::1', 'DMC-Auditoria/3.0 (SimBrowser)', '2026-03-20 11:53:46.896078-03');
INSERT INTO public.tb_auditoria VALUES (645, 'login', 'sessao', 83, 'audit_op2', 83, 'audit_op2', 'operador', '[]', '[]', '::1', 'DMC-Auditoria/3.0 (SimBrowser)', '2026-03-20 11:53:50.241072-03');
INSERT INTO public.tb_auditoria VALUES (646, 'excluir', 'conexao', 74, NULL, 1, 'admin', 'super_admin', '[]', '[]', '::1', 'DMC-Auditoria/3.0 (SimBrowser)', '2026-03-20 11:53:55.021656-03');
INSERT INTO public.tb_auditoria VALUES (647, 'excluir', 'conexao', 75, NULL, 1, 'admin', 'super_admin', '[]', '[]', '::1', 'DMC-Auditoria/3.0 (SimBrowser)', '2026-03-20 11:53:55.532742-03');
INSERT INTO public.tb_auditoria VALUES (648, 'excluir', 'conexao', 76, NULL, 1, 'admin', 'super_admin', '[]', '[]', '::1', 'DMC-Auditoria/3.0 (SimBrowser)', '2026-03-20 11:53:56.088268-03');
INSERT INTO public.tb_auditoria VALUES (649, 'excluir', 'usuario', 78, 'audit_admin1', 1, 'admin', 'super_admin', '[]', '[]', '::1', 'DMC-Auditoria/3.0 (SimBrowser)', '2026-03-20 11:53:56.249593-03');
INSERT INTO public.tb_auditoria VALUES (650, 'excluir', 'usuario', 79, 'audit_admin2', 1, 'admin', 'super_admin', '[]', '[]', '::1', 'DMC-Auditoria/3.0 (SimBrowser)', '2026-03-20 11:53:56.434316-03');
INSERT INTO public.tb_auditoria VALUES (651, 'excluir', 'usuario', 80, 'audit_dev1', 1, 'admin', 'super_admin', '[]', '[]', '::1', 'DMC-Auditoria/3.0 (SimBrowser)', '2026-03-20 11:53:56.603868-03');
INSERT INTO public.tb_auditoria VALUES (652, 'excluir', 'usuario', 81, 'audit_dev2', 1, 'admin', 'super_admin', '[]', '[]', '::1', 'DMC-Auditoria/3.0 (SimBrowser)', '2026-03-20 11:53:56.763412-03');
INSERT INTO public.tb_auditoria VALUES (653, 'excluir', 'usuario', 82, 'audit_op1', 1, 'admin', 'super_admin', '[]', '[]', '::1', 'DMC-Auditoria/3.0 (SimBrowser)', '2026-03-20 11:53:56.912724-03');
INSERT INTO public.tb_auditoria VALUES (654, 'excluir', 'usuario', 83, 'audit_op2', 1, 'admin', 'super_admin', '[]', '[]', '::1', 'DMC-Auditoria/3.0 (SimBrowser)', '2026-03-20 11:53:57.057301-03');
INSERT INTO public.tb_auditoria VALUES (655, 'excluir', 'empresa', 44, 'Secretaria de TI', 1, 'admin', 'super_admin', '[]', '[]', '::1', 'DMC-Auditoria/3.0 (SimBrowser)', '2026-03-20 11:53:58.725882-03');
INSERT INTO public.tb_auditoria VALUES (656, 'excluir', 'empresa', 45, 'Secretaria de Saúde', 1, 'admin', 'super_admin', '[]', '[]', '::1', 'DMC-Auditoria/3.0 (SimBrowser)', '2026-03-20 11:53:58.867631-03');
INSERT INTO public.tb_auditoria VALUES (657, 'excluir', 'empresa', 46, 'Secretaria de Educação', 1, 'admin', 'super_admin', '[]', '[]', '::1', 'DMC-Auditoria/3.0 (SimBrowser)', '2026-03-20 11:53:59.024097-03');
INSERT INTO public.tb_auditoria VALUES (658, 'excluir', 'empresa', 47, 'Secretaria de Finanças', 1, 'admin', 'super_admin', '[]', '[]', '::1', 'DMC-Auditoria/3.0 (SimBrowser)', '2026-03-20 11:53:59.175293-03');
INSERT INTO public.tb_auditoria VALUES (659, 'excluir', 'empresa', 48, 'Secretaria de Obras', 1, 'admin', 'super_admin', '[]', '[]', '::1', 'DMC-Auditoria/3.0 (SimBrowser)', '2026-03-20 11:53:59.31326-03');
INSERT INTO public.tb_auditoria VALUES (660, 'login', 'sessao', 1, 'admin', 1, 'admin', 'super_admin', '[]', '[]', '::1', '', '2026-03-20 11:54:05.17406-03');
INSERT INTO public.tb_auditoria VALUES (661, 'criar', 'empresa', 49, 'Empresa Teste E2E', 1, 'admin', 'super_admin', '[]', '[]', '::1', '', '2026-03-20 11:54:06.774213-03');
INSERT INTO public.tb_auditoria VALUES (662, 'criar', 'usuario', 84, 'testuser_e2e', 1, 'admin', 'super_admin', '[]', '{"nivel_acesso": "operador"}', '::1', '', '2026-03-20 11:54:07.47346-03');
INSERT INTO public.tb_auditoria VALUES (663, 'login', 'sessao', 84, 'testuser_e2e', 84, 'testuser_e2e', 'operador', '[]', '[]', '::1', '', '2026-03-20 11:54:07.873736-03');
INSERT INTO public.tb_auditoria VALUES (664, 'logout', 'sessao', NULL, NULL, 84, 'testuser_e2e', 'operador', '[]', '[]', '::1', '', '2026-03-20 11:54:08.06564-03');
INSERT INTO public.tb_auditoria VALUES (665, 'login', 'sessao', 84, 'testuser_e2e', 84, 'testuser_e2e', 'operador', '[]', '[]', '::1', '', '2026-03-20 11:54:08.464584-03');
INSERT INTO public.tb_auditoria VALUES (666, 'logout', 'sessao', NULL, NULL, 84, 'testuser_e2e', 'operador', '[]', '[]', '::1', '', '2026-03-20 11:54:08.574935-03');
INSERT INTO public.tb_auditoria VALUES (667, 'criar', 'conexao', 77, '', 1, 'admin', 'super_admin', '[]', '[]', '::1', '', '2026-03-20 11:54:09.175909-03');
INSERT INTO public.tb_auditoria VALUES (668, 'excluir', 'conexao', 77, NULL, 1, 'admin', 'super_admin', '[]', '[]', '::1', '', '2026-03-20 11:54:14.296476-03');
INSERT INTO public.tb_auditoria VALUES (669, 'excluir', 'usuario', 84, 'testuser_e2e', 1, 'admin', 'super_admin', '[]', '[]', '::1', '', '2026-03-20 11:54:14.435085-03');
INSERT INTO public.tb_auditoria VALUES (670, 'excluir', 'empresa', 49, 'Empresa Teste E2E', 1, 'admin', 'super_admin', '[]', '[]', '::1', '', '2026-03-20 11:54:14.721762-03');
INSERT INTO public.tb_auditoria VALUES (671, 'login', 'sessao', 1, 'admin', 1, 'admin', 'super_admin', '[]', '[]', '::1', '', '2026-03-20 11:54:21.229066-03');
INSERT INTO public.tb_auditoria VALUES (672, 'login', 'sessao', 1, 'admin', 1, 'admin', 'super_admin', '[]', '[]', '::1', '', '2026-03-20 11:54:23.835949-03');
INSERT INTO public.tb_auditoria VALUES (673, 'login', 'sessao', 1, 'admin', 1, 'admin', 'super_admin', '[]', '[]', '::1', '', '2026-03-20 11:54:28.889549-03');
INSERT INTO public.tb_auditoria VALUES (674, 'login', 'sessao', 85, 'test_dev', 85, 'test_dev', 'desenvolvedor', '[]', '[]', '::1', '', '2026-03-20 11:54:29.127311-03');
INSERT INTO public.tb_auditoria VALUES (675, 'login', 'sessao', 86, 'test_op', 86, 'test_op', 'operador', '[]', '[]', '::1', '', '2026-03-20 11:54:29.341166-03');
INSERT INTO public.tb_auditoria VALUES (676, 'login', 'sessao', 1, 'admin', 1, 'admin', 'super_admin', '[]', '[]', '::1', '', '2026-03-20 11:54:33.273068-03');
INSERT INTO public.tb_auditoria VALUES (677, 'criar', 'pipeline', 27, 'Pipeline Teste E2E', 1, 'admin', 'super_admin', '[]', '[]', '::1', '', '2026-03-20 11:54:33.617768-03');
INSERT INTO public.tb_auditoria VALUES (678, 'excluir', 'pipeline', 27, NULL, 1, 'admin', 'super_admin', '[]', '[]', '::1', '', '2026-03-20 11:54:33.891629-03');
INSERT INTO public.tb_auditoria VALUES (679, 'criar', 'workflow', 19, 'Workflow Teste E2E', 1, 'admin', 'super_admin', '[]', '[]', '::1', '', '2026-03-20 11:54:34.182449-03');
INSERT INTO public.tb_auditoria VALUES (680, 'excluir', 'workflow', 19, NULL, 1, 'admin', 'super_admin', '[]', '[]', '::1', '', '2026-03-20 11:54:34.61131-03');
INSERT INTO public.tb_auditoria VALUES (681, 'login', 'sessao', 1, 'admin', 1, 'admin', 'super_admin', '[]', '[]', '::1', '', '2026-03-20 11:54:37.238837-03');
INSERT INTO public.tb_auditoria VALUES (682, 'editar', 'configuracao', NULL, 'Grupo: geral', 1, 'admin', 'super_admin', '{"app_nome": "DMC DataLoad"}', '{"app_nome": "DMC DataLoad"}', '::1', '', '2026-03-20 11:54:37.753191-03');
INSERT INTO public.tb_auditoria VALUES (683, 'editar', 'conexao', 16, '', 1, 'admin', 'super_admin', '[]', '[]', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-20 11:54:42.407864-03');
INSERT INTO public.tb_auditoria VALUES (684, 'criar', 'pipeline', 28, 'Novo Pipeline', 1, 'admin', 'super_admin', '[]', '[]', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-20 12:59:08.237995-03');
INSERT INTO public.tb_auditoria VALUES (685, 'logout', 'sessao', NULL, NULL, 23, 'renan', 'operador', '[]', '[]', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-20 13:04:01.210106-03');
INSERT INTO public.tb_auditoria VALUES (686, 'login', 'sessao', 23, 'renan', 23, 'renan', 'operador', '[]', '[]', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-20 13:04:07.852273-03');
INSERT INTO public.tb_auditoria VALUES (687, 'login', 'sessao', 13, 'caio', 13, 'caio', 'desenvolvedor', '[]', '[]', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', '2026-03-20 13:12:14.803449-03');
INSERT INTO public.tb_auditoria VALUES (688, 'login', 'sessao', 1, 'admin', 1, 'admin', 'super_admin', '[]', '[]', '::1', '', '2026-03-20 13:13:03.309667-03');
INSERT INTO public.tb_auditoria VALUES (689, 'login', 'sessao', 1, 'admin', 1, 'admin', 'super_admin', '[]', '[]', '::1', 'DMC-BrowserTest/2.0 (Simulated Browser)', '2026-03-20 13:13:16.677245-03');
INSERT INTO public.tb_auditoria VALUES (690, 'criar', 'empresa', 50, 'DMC Tecnologia', 1, 'admin', 'super_admin', '[]', '[]', '::1', 'DMC-BrowserTest/2.0 (Simulated Browser)', '2026-03-20 13:13:16.978874-03');
INSERT INTO public.tb_auditoria VALUES (691, 'criar', 'empresa', 51, 'Alpha Corp', 1, 'admin', 'super_admin', '[]', '[]', '::1', 'DMC-BrowserTest/2.0 (Simulated Browser)', '2026-03-20 13:13:17.148905-03');
INSERT INTO public.tb_auditoria VALUES (692, 'criar', 'empresa', 52, 'Beta Sistemas', 1, 'admin', 'super_admin', '[]', '[]', '::1', 'DMC-BrowserTest/2.0 (Simulated Browser)', '2026-03-20 13:13:17.327228-03');
INSERT INTO public.tb_auditoria VALUES (693, 'criar', 'usuario', 87, 'admin_browser_test', 1, 'admin', 'super_admin', '[]', '{"nivel_acesso": "admin"}', '::1', 'DMC-BrowserTest/2.0 (Simulated Browser)', '2026-03-20 13:13:18.788825-03');
INSERT INTO public.tb_auditoria VALUES (694, 'criar', 'usuario', 88, 'dev_browser_test', 1, 'admin', 'super_admin', '[]', '{"nivel_acesso": "desenvolvedor"}', '::1', 'DMC-BrowserTest/2.0 (Simulated Browser)', '2026-03-20 13:13:19.029303-03');
INSERT INTO public.tb_auditoria VALUES (695, 'criar', 'usuario', 89, 'op_browser_test', 1, 'admin', 'super_admin', '[]', '{"nivel_acesso": "operador"}', '::1', 'DMC-BrowserTest/2.0 (Simulated Browser)', '2026-03-20 13:13:19.291175-03');
INSERT INTO public.tb_auditoria VALUES (696, 'criar', 'conexao', 78, '', 1, 'admin', 'super_admin', '[]', '[]', '::1', 'DMC-BrowserTest/2.0 (Simulated Browser)', '2026-03-20 13:13:25.314763-03');
INSERT INTO public.tb_auditoria VALUES (697, 'login', 'sessao', 87, 'admin_browser_test', 87, 'admin_browser_test', 'admin', '[]', '[]', '::1', 'DMC-BrowserTest/2.0 (Simulated Browser)', '2026-03-20 13:13:26.2974-03');
INSERT INTO public.tb_auditoria VALUES (698, 'criar', 'conexao', 79, '', 87, 'admin_browser_test', 'admin', '[]', '[]', '::1', 'DMC-BrowserTest/2.0 (Simulated Browser)', '2026-03-20 13:13:27.911889-03');
INSERT INTO public.tb_auditoria VALUES (699, 'login', 'sessao', 88, 'dev_browser_test', 88, 'dev_browser_test', 'desenvolvedor', '[]', '[]', '::1', 'DMC-BrowserTest/2.0 (Simulated Browser)', '2026-03-20 13:13:28.306917-03');
INSERT INTO public.tb_auditoria VALUES (700, 'criar', 'conexao', 80, '', 88, 'dev_browser_test', 'desenvolvedor', '[]', '[]', '::1', 'DMC-BrowserTest/2.0 (Simulated Browser)', '2026-03-20 13:13:29.71699-03');
INSERT INTO public.tb_auditoria VALUES (701, 'login', 'sessao', 89, 'op_browser_test', 89, 'op_browser_test', 'operador', '[]', '[]', '::1', 'DMC-BrowserTest/2.0 (Simulated Browser)', '2026-03-20 13:13:30.245552-03');
INSERT INTO public.tb_auditoria VALUES (702, 'excluir', 'conexao', 78, NULL, 1, 'admin', 'super_admin', '[]', '[]', '::1', 'DMC-BrowserTest/2.0 (Simulated Browser)', '2026-03-20 13:13:34.886162-03');
INSERT INTO public.tb_auditoria VALUES (703, 'excluir', 'conexao', 79, NULL, 1, 'admin', 'super_admin', '[]', '[]', '::1', 'DMC-BrowserTest/2.0 (Simulated Browser)', '2026-03-20 13:13:35.585166-03');
INSERT INTO public.tb_auditoria VALUES (704, 'excluir', 'conexao', 80, NULL, 1, 'admin', 'super_admin', '[]', '[]', '::1', 'DMC-BrowserTest/2.0 (Simulated Browser)', '2026-03-20 13:13:36.193495-03');
INSERT INTO public.tb_auditoria VALUES (705, 'excluir', 'usuario', 87, 'admin_browser_test', 1, 'admin', 'super_admin', '[]', '[]', '::1', 'DMC-BrowserTest/2.0 (Simulated Browser)', '2026-03-20 13:13:36.354129-03');
INSERT INTO public.tb_auditoria VALUES (706, 'excluir', 'usuario', 88, 'dev_browser_test', 1, 'admin', 'super_admin', '[]', '[]', '::1', 'DMC-BrowserTest/2.0 (Simulated Browser)', '2026-03-20 13:13:36.506368-03');
INSERT INTO public.tb_auditoria VALUES (707, 'excluir', 'usuario', 89, 'op_browser_test', 1, 'admin', 'super_admin', '[]', '[]', '::1', 'DMC-BrowserTest/2.0 (Simulated Browser)', '2026-03-20 13:13:36.652292-03');
INSERT INTO public.tb_auditoria VALUES (708, 'excluir', 'empresa', 50, 'DMC Tecnologia', 1, 'admin', 'super_admin', '[]', '[]', '::1', 'DMC-BrowserTest/2.0 (Simulated Browser)', '2026-03-20 13:13:37.816347-03');
INSERT INTO public.tb_auditoria VALUES (709, 'excluir', 'empresa', 51, 'Alpha Corp', 1, 'admin', 'super_admin', '[]', '[]', '::1', 'DMC-BrowserTest/2.0 (Simulated Browser)', '2026-03-20 13:13:37.955988-03');
INSERT INTO public.tb_auditoria VALUES (710, 'excluir', 'empresa', 52, 'Beta Sistemas', 1, 'admin', 'super_admin', '[]', '[]', '::1', 'DMC-BrowserTest/2.0 (Simulated Browser)', '2026-03-20 13:13:38.098998-03');
INSERT INTO public.tb_auditoria VALUES (711, 'login', 'sessao', 1, 'admin', 1, 'admin', 'super_admin', '[]', '[]', '::1', 'DMC-BrowserTest/2.0 (Simulated Browser)', '2026-03-20 13:16:12.131459-03');
INSERT INTO public.tb_auditoria VALUES (712, 'criar', 'empresa', 53, 'DMC Tecnologia', 1, 'admin', 'super_admin', '[]', '[]', '::1', 'DMC-BrowserTest/2.0 (Simulated Browser)', '2026-03-20 13:16:12.379788-03');
INSERT INTO public.tb_auditoria VALUES (713, 'criar', 'empresa', 54, 'Alpha Corp', 1, 'admin', 'super_admin', '[]', '[]', '::1', 'DMC-BrowserTest/2.0 (Simulated Browser)', '2026-03-20 13:16:12.503839-03');
INSERT INTO public.tb_auditoria VALUES (714, 'criar', 'empresa', 55, 'Beta Sistemas', 1, 'admin', 'super_admin', '[]', '[]', '::1', 'DMC-BrowserTest/2.0 (Simulated Browser)', '2026-03-20 13:16:12.633524-03');
INSERT INTO public.tb_auditoria VALUES (715, 'criar', 'usuario', 90, 'admin_browser_test', 1, 'admin', 'super_admin', '[]', '{"nivel_acesso": "admin"}', '::1', 'DMC-BrowserTest/2.0 (Simulated Browser)', '2026-03-20 13:16:13.961963-03');
INSERT INTO public.tb_auditoria VALUES (716, 'criar', 'usuario', 91, 'dev_browser_test', 1, 'admin', 'super_admin', '[]', '{"nivel_acesso": "desenvolvedor"}', '::1', 'DMC-BrowserTest/2.0 (Simulated Browser)', '2026-03-20 13:16:14.159437-03');
INSERT INTO public.tb_auditoria VALUES (717, 'criar', 'usuario', 92, 'op_browser_test', 1, 'admin', 'super_admin', '[]', '{"nivel_acesso": "operador"}', '::1', 'DMC-BrowserTest/2.0 (Simulated Browser)', '2026-03-20 13:16:14.351034-03');
INSERT INTO public.tb_auditoria VALUES (718, 'criar', 'conexao', 81, '', 1, 'admin', 'super_admin', '[]', '[]', '::1', 'DMC-BrowserTest/2.0 (Simulated Browser)', '2026-03-20 13:16:20.094552-03');
INSERT INTO public.tb_auditoria VALUES (719, 'login', 'sessao', 90, 'admin_browser_test', 90, 'admin_browser_test', 'admin', '[]', '[]', '::1', 'DMC-BrowserTest/2.0 (Simulated Browser)', '2026-03-20 13:16:20.937898-03');
INSERT INTO public.tb_auditoria VALUES (720, 'criar', 'conexao', 82, '', 90, 'admin_browser_test', 'admin', '[]', '[]', '::1', 'DMC-BrowserTest/2.0 (Simulated Browser)', '2026-03-20 13:16:22.570098-03');
INSERT INTO public.tb_auditoria VALUES (721, 'login', 'sessao', 91, 'dev_browser_test', 91, 'dev_browser_test', 'desenvolvedor', '[]', '[]', '::1', 'DMC-BrowserTest/2.0 (Simulated Browser)', '2026-03-20 13:16:22.909545-03');
INSERT INTO public.tb_auditoria VALUES (722, 'criar', 'conexao', 83, '', 91, 'dev_browser_test', 'desenvolvedor', '[]', '[]', '::1', 'DMC-BrowserTest/2.0 (Simulated Browser)', '2026-03-20 13:16:24.308673-03');
INSERT INTO public.tb_auditoria VALUES (723, 'login', 'sessao', 92, 'op_browser_test', 92, 'op_browser_test', 'operador', '[]', '[]', '::1', 'DMC-BrowserTest/2.0 (Simulated Browser)', '2026-03-20 13:16:24.840114-03');
INSERT INTO public.tb_auditoria VALUES (724, 'excluir', 'conexao', 81, NULL, 1, 'admin', 'super_admin', '[]', '[]', '::1', 'DMC-BrowserTest/2.0 (Simulated Browser)', '2026-03-20 13:16:29.71762-03');
INSERT INTO public.tb_auditoria VALUES (725, 'excluir', 'conexao', 82, NULL, 1, 'admin', 'super_admin', '[]', '[]', '::1', 'DMC-BrowserTest/2.0 (Simulated Browser)', '2026-03-20 13:16:30.178287-03');
INSERT INTO public.tb_auditoria VALUES (726, 'excluir', 'conexao', 83, NULL, 1, 'admin', 'super_admin', '[]', '[]', '::1', 'DMC-BrowserTest/2.0 (Simulated Browser)', '2026-03-20 13:16:30.647124-03');
INSERT INTO public.tb_auditoria VALUES (727, 'excluir', 'usuario', 90, 'admin_browser_test', 1, 'admin', 'super_admin', '[]', '[]', '::1', 'DMC-BrowserTest/2.0 (Simulated Browser)', '2026-03-20 13:16:30.795713-03');
INSERT INTO public.tb_auditoria VALUES (728, 'excluir', 'usuario', 91, 'dev_browser_test', 1, 'admin', 'super_admin', '[]', '[]', '::1', 'DMC-BrowserTest/2.0 (Simulated Browser)', '2026-03-20 13:16:30.951673-03');
INSERT INTO public.tb_auditoria VALUES (729, 'excluir', 'usuario', 92, 'op_browser_test', 1, 'admin', 'super_admin', '[]', '[]', '::1', 'DMC-BrowserTest/2.0 (Simulated Browser)', '2026-03-20 13:16:31.132295-03');
INSERT INTO public.tb_auditoria VALUES (730, 'excluir', 'empresa', 53, 'DMC Tecnologia', 1, 'admin', 'super_admin', '[]', '[]', '::1', 'DMC-BrowserTest/2.0 (Simulated Browser)', '2026-03-20 13:16:32.274363-03');
INSERT INTO public.tb_auditoria VALUES (731, 'excluir', 'empresa', 54, 'Alpha Corp', 1, 'admin', 'super_admin', '[]', '[]', '::1', 'DMC-BrowserTest/2.0 (Simulated Browser)', '2026-03-20 13:16:32.416524-03');
INSERT INTO public.tb_auditoria VALUES (732, 'excluir', 'empresa', 55, 'Beta Sistemas', 1, 'admin', 'super_admin', '[]', '[]', '::1', 'DMC-BrowserTest/2.0 (Simulated Browser)', '2026-03-20 13:16:32.548296-03');
INSERT INTO public.tb_auditoria VALUES (733, 'login', 'sessao', 1, 'admin', 1, 'admin', 'super_admin', '[]', '[]', '::1', 'DMC-Auditoria/3.0 (SimBrowser)', '2026-03-20 13:16:37.831931-03');
INSERT INTO public.tb_auditoria VALUES (734, 'criar', 'empresa', 56, 'Secretaria de TI', 1, 'admin', 'super_admin', '[]', '[]', '::1', 'DMC-Auditoria/3.0 (SimBrowser)', '2026-03-20 13:16:38.069495-03');
INSERT INTO public.tb_auditoria VALUES (735, 'criar', 'empresa', 57, 'Secretaria de Saúde', 1, 'admin', 'super_admin', '[]', '[]', '::1', 'DMC-Auditoria/3.0 (SimBrowser)', '2026-03-20 13:16:38.18045-03');
INSERT INTO public.tb_auditoria VALUES (736, 'criar', 'empresa', 58, 'Secretaria de Educação', 1, 'admin', 'super_admin', '[]', '[]', '::1', 'DMC-Auditoria/3.0 (SimBrowser)', '2026-03-20 13:16:38.319312-03');
INSERT INTO public.tb_auditoria VALUES (737, 'criar', 'empresa', 59, 'Secretaria de Finanças', 1, 'admin', 'super_admin', '[]', '[]', '::1', 'DMC-Auditoria/3.0 (SimBrowser)', '2026-03-20 13:16:38.481895-03');
INSERT INTO public.tb_auditoria VALUES (738, 'criar', 'empresa', 60, 'Secretaria de Obras', 1, 'admin', 'super_admin', '[]', '[]', '::1', 'DMC-Auditoria/3.0 (SimBrowser)', '2026-03-20 13:16:38.623255-03');
INSERT INTO public.tb_auditoria VALUES (739, 'criar', 'usuario', 93, 'audit_admin1', 1, 'admin', 'super_admin', '[]', '{"nivel_acesso": "admin"}', '::1', 'DMC-Auditoria/3.0 (SimBrowser)', '2026-03-20 13:16:40.530477-03');
INSERT INTO public.tb_auditoria VALUES (740, 'criar', 'usuario', 94, 'audit_admin2', 1, 'admin', 'super_admin', '[]', '{"nivel_acesso": "admin"}', '::1', 'DMC-Auditoria/3.0 (SimBrowser)', '2026-03-20 13:16:40.73086-03');
INSERT INTO public.tb_auditoria VALUES (741, 'criar', 'usuario', 95, 'audit_dev1', 1, 'admin', 'super_admin', '[]', '{"nivel_acesso": "desenvolvedor"}', '::1', 'DMC-Auditoria/3.0 (SimBrowser)', '2026-03-20 13:16:40.941923-03');
INSERT INTO public.tb_auditoria VALUES (742, 'criar', 'usuario', 96, 'audit_dev2', 1, 'admin', 'super_admin', '[]', '{"nivel_acesso": "desenvolvedor"}', '::1', 'DMC-Auditoria/3.0 (SimBrowser)', '2026-03-20 13:16:41.163922-03');
INSERT INTO public.tb_auditoria VALUES (743, 'criar', 'usuario', 97, 'audit_op1', 1, 'admin', 'super_admin', '[]', '{"nivel_acesso": "operador"}', '::1', 'DMC-Auditoria/3.0 (SimBrowser)', '2026-03-20 13:16:41.377972-03');
INSERT INTO public.tb_auditoria VALUES (744, 'criar', 'usuario', 98, 'audit_op2', 1, 'admin', 'super_admin', '[]', '{"nivel_acesso": "operador"}', '::1', 'DMC-Auditoria/3.0 (SimBrowser)', '2026-03-20 13:16:41.589758-03');
INSERT INTO public.tb_auditoria VALUES (745, 'criar', 'conexao', 84, '', 1, 'admin', 'super_admin', '[]', '[]', '::1', 'DMC-Auditoria/3.0 (SimBrowser)', '2026-03-20 13:16:42.88522-03');
INSERT INTO public.tb_auditoria VALUES (746, 'criar', 'conexao', 85, '', 1, 'admin', 'super_admin', '[]', '[]', '::1', 'DMC-Auditoria/3.0 (SimBrowser)', '2026-03-20 13:16:43.432705-03');
INSERT INTO public.tb_auditoria VALUES (747, 'login', 'sessao', 93, 'audit_admin1', 93, 'audit_admin1', 'admin', '[]', '[]', '::1', 'DMC-Auditoria/3.0 (SimBrowser)', '2026-03-20 13:16:47.498053-03');
INSERT INTO public.tb_auditoria VALUES (748, 'login', 'sessao', 94, 'audit_admin2', 94, 'audit_admin2', 'admin', '[]', '[]', '::1', 'DMC-Auditoria/3.0 (SimBrowser)', '2026-03-20 13:16:49.384178-03');
INSERT INTO public.tb_auditoria VALUES (749, 'login', 'sessao', 95, 'audit_dev1', 95, 'audit_dev1', 'desenvolvedor', '[]', '[]', '::1', 'DMC-Auditoria/3.0 (SimBrowser)', '2026-03-20 13:16:49.898232-03');
INSERT INTO public.tb_auditoria VALUES (750, 'criar', 'conexao', 86, '', 95, 'audit_dev1', 'desenvolvedor', '[]', '[]', '::1', 'DMC-Auditoria/3.0 (SimBrowser)', '2026-03-20 13:16:51.262473-03');
INSERT INTO public.tb_auditoria VALUES (751, 'login', 'sessao', 96, 'audit_dev2', 96, 'audit_dev2', 'desenvolvedor', '[]', '[]', '::1', 'DMC-Auditoria/3.0 (SimBrowser)', '2026-03-20 13:16:51.478102-03');
INSERT INTO public.tb_auditoria VALUES (752, 'login', 'sessao', 97, 'audit_op1', 97, 'audit_op1', 'operador', '[]', '[]', '::1', 'DMC-Auditoria/3.0 (SimBrowser)', '2026-03-20 13:16:52.481518-03');
INSERT INTO public.tb_auditoria VALUES (753, 'login', 'sessao', 98, 'audit_op2', 98, 'audit_op2', 'operador', '[]', '[]', '::1', 'DMC-Auditoria/3.0 (SimBrowser)', '2026-03-20 13:16:55.268751-03');
INSERT INTO public.tb_auditoria VALUES (754, 'excluir', 'conexao', 84, NULL, 1, 'admin', 'super_admin', '[]', '[]', '::1', 'DMC-Auditoria/3.0 (SimBrowser)', '2026-03-20 13:16:59.276774-03');
INSERT INTO public.tb_auditoria VALUES (755, 'excluir', 'conexao', 85, NULL, 1, 'admin', 'super_admin', '[]', '[]', '::1', 'DMC-Auditoria/3.0 (SimBrowser)', '2026-03-20 13:16:59.805193-03');
INSERT INTO public.tb_auditoria VALUES (756, 'excluir', 'conexao', 86, NULL, 1, 'admin', 'super_admin', '[]', '[]', '::1', 'DMC-Auditoria/3.0 (SimBrowser)', '2026-03-20 13:17:00.241797-03');
INSERT INTO public.tb_auditoria VALUES (757, 'excluir', 'usuario', 93, 'audit_admin1', 1, 'admin', 'super_admin', '[]', '[]', '::1', 'DMC-Auditoria/3.0 (SimBrowser)', '2026-03-20 13:17:00.374403-03');
INSERT INTO public.tb_auditoria VALUES (758, 'excluir', 'usuario', 94, 'audit_admin2', 1, 'admin', 'super_admin', '[]', '[]', '::1', 'DMC-Auditoria/3.0 (SimBrowser)', '2026-03-20 13:17:00.512009-03');
INSERT INTO public.tb_auditoria VALUES (759, 'excluir', 'usuario', 95, 'audit_dev1', 1, 'admin', 'super_admin', '[]', '[]', '::1', 'DMC-Auditoria/3.0 (SimBrowser)', '2026-03-20 13:17:00.654595-03');
INSERT INTO public.tb_auditoria VALUES (760, 'excluir', 'usuario', 96, 'audit_dev2', 1, 'admin', 'super_admin', '[]', '[]', '::1', 'DMC-Auditoria/3.0 (SimBrowser)', '2026-03-20 13:17:00.791855-03');
INSERT INTO public.tb_auditoria VALUES (761, 'excluir', 'usuario', 97, 'audit_op1', 1, 'admin', 'super_admin', '[]', '[]', '::1', 'DMC-Auditoria/3.0 (SimBrowser)', '2026-03-20 13:17:00.949382-03');
INSERT INTO public.tb_auditoria VALUES (762, 'excluir', 'usuario', 98, 'audit_op2', 1, 'admin', 'super_admin', '[]', '[]', '::1', 'DMC-Auditoria/3.0 (SimBrowser)', '2026-03-20 13:17:01.108856-03');
INSERT INTO public.tb_auditoria VALUES (763, 'excluir', 'empresa', 56, 'Secretaria de TI', 1, 'admin', 'super_admin', '[]', '[]', '::1', 'DMC-Auditoria/3.0 (SimBrowser)', '2026-03-20 13:17:02.623693-03');
INSERT INTO public.tb_auditoria VALUES (764, 'excluir', 'empresa', 57, 'Secretaria de Saúde', 1, 'admin', 'super_admin', '[]', '[]', '::1', 'DMC-Auditoria/3.0 (SimBrowser)', '2026-03-20 13:17:02.772259-03');
INSERT INTO public.tb_auditoria VALUES (765, 'excluir', 'empresa', 58, 'Secretaria de Educação', 1, 'admin', 'super_admin', '[]', '[]', '::1', 'DMC-Auditoria/3.0 (SimBrowser)', '2026-03-20 13:17:02.926648-03');
INSERT INTO public.tb_auditoria VALUES (766, 'excluir', 'empresa', 59, 'Secretaria de Finanças', 1, 'admin', 'super_admin', '[]', '[]', '::1', 'DMC-Auditoria/3.0 (SimBrowser)', '2026-03-20 13:17:03.068118-03');
INSERT INTO public.tb_auditoria VALUES (767, 'excluir', 'empresa', 60, 'Secretaria de Obras', 1, 'admin', 'super_admin', '[]', '[]', '::1', 'DMC-Auditoria/3.0 (SimBrowser)', '2026-03-20 13:17:03.224963-03');
INSERT INTO public.tb_auditoria VALUES (768, 'login', 'sessao', 1, 'admin', 1, 'admin', 'super_admin', '[]', '[]', '::1', '', '2026-03-20 13:17:08.667418-03');
INSERT INTO public.tb_auditoria VALUES (769, 'criar', 'empresa', 61, 'Empresa Teste E2E', 1, 'admin', 'super_admin', '[]', '[]', '::1', '', '2026-03-20 13:17:09.900777-03');
INSERT INTO public.tb_auditoria VALUES (770, 'criar', 'usuario', 99, 'testuser_e2e', 1, 'admin', 'super_admin', '[]', '{"nivel_acesso": "operador"}', '::1', '', '2026-03-20 13:17:10.536476-03');
INSERT INTO public.tb_auditoria VALUES (771, 'login', 'sessao', 99, 'testuser_e2e', 99, 'testuser_e2e', 'operador', '[]', '[]', '::1', '', '2026-03-20 13:17:10.985822-03');
INSERT INTO public.tb_auditoria VALUES (772, 'logout', 'sessao', NULL, NULL, 99, 'testuser_e2e', 'operador', '[]', '[]', '::1', '', '2026-03-20 13:17:11.184119-03');
INSERT INTO public.tb_auditoria VALUES (773, 'login', 'sessao', 99, 'testuser_e2e', 99, 'testuser_e2e', 'operador', '[]', '[]', '::1', '', '2026-03-20 13:17:11.61839-03');
INSERT INTO public.tb_auditoria VALUES (774, 'logout', 'sessao', NULL, NULL, 99, 'testuser_e2e', 'operador', '[]', '[]', '::1', '', '2026-03-20 13:17:11.731189-03');
INSERT INTO public.tb_auditoria VALUES (775, 'criar', 'conexao', 87, '', 1, 'admin', 'super_admin', '[]', '[]', '::1', '', '2026-03-20 13:17:12.316746-03');
INSERT INTO public.tb_auditoria VALUES (776, 'excluir', 'conexao', 87, NULL, 1, 'admin', 'super_admin', '[]', '[]', '::1', '', '2026-03-20 13:17:17.554541-03');
INSERT INTO public.tb_auditoria VALUES (777, 'excluir', 'usuario', 99, 'testuser_e2e', 1, 'admin', 'super_admin', '[]', '[]', '::1', '', '2026-03-20 13:17:17.712059-03');
INSERT INTO public.tb_auditoria VALUES (778, 'excluir', 'empresa', 61, 'Empresa Teste E2E', 1, 'admin', 'super_admin', '[]', '[]', '::1', '', '2026-03-20 13:17:17.971744-03');
INSERT INTO public.tb_auditoria VALUES (779, 'login', 'sessao', 1, 'admin', 1, 'admin', 'super_admin', '[]', '[]', '::1', '', '2026-03-20 13:17:23.207695-03');
INSERT INTO public.tb_auditoria VALUES (780, 'login', 'sessao', 1, 'admin', 1, 'admin', 'super_admin', '[]', '[]', '::1', '', '2026-03-20 13:17:32.129836-03');
INSERT INTO public.tb_auditoria VALUES (781, 'login', 'sessao', 100, 'test_dev', 100, 'test_dev', 'desenvolvedor', '[]', '[]', '::1', '', '2026-03-20 13:17:32.297816-03');
INSERT INTO public.tb_auditoria VALUES (782, 'login', 'sessao', 101, 'test_op', 101, 'test_op', 'operador', '[]', '[]', '::1', '', '2026-03-20 13:17:32.464454-03');
INSERT INTO public.tb_auditoria VALUES (783, 'login', 'sessao', 1, 'admin', 1, 'admin', 'super_admin', '[]', '[]', '::1', '', '2026-03-20 13:17:40.429406-03');
INSERT INTO public.tb_auditoria VALUES (784, 'criar', 'pipeline', 29, 'Pipeline Teste E2E', 1, 'admin', 'super_admin', '[]', '[]', '::1', '', '2026-03-20 13:17:40.630334-03');
INSERT INTO public.tb_auditoria VALUES (785, 'excluir', 'pipeline', 29, NULL, 1, 'admin', 'super_admin', '[]', '[]', '::1', '', '2026-03-20 13:17:40.793581-03');
INSERT INTO public.tb_auditoria VALUES (786, 'criar', 'workflow', 20, 'Workflow Teste E2E', 1, 'admin', 'super_admin', '[]', '[]', '::1', '', '2026-03-20 13:17:41.026392-03');
INSERT INTO public.tb_auditoria VALUES (787, 'excluir', 'workflow', 20, NULL, 1, 'admin', 'super_admin', '[]', '[]', '::1', '', '2026-03-20 13:17:41.350152-03');
INSERT INTO public.tb_auditoria VALUES (788, 'login', 'sessao', 1, 'admin', 1, 'admin', 'super_admin', '[]', '[]', '::1', '', '2026-03-20 13:17:48.213664-03');
INSERT INTO public.tb_auditoria VALUES (789, 'editar', 'configuracao', NULL, 'Grupo: geral', 1, 'admin', 'super_admin', '{"app_nome": "DMC DataLoad"}', '{"app_nome": "DMC DataLoad"}', '::1', '', '2026-03-20 13:17:48.663783-03');
INSERT INTO public.tb_auditoria VALUES (790, 'login', 'sessao', 1, 'admin', 1, 'admin', 'super_admin', '[]', '[]', '::1', '', '2026-03-20 13:17:55.711539-03');
INSERT INTO public.tb_auditoria VALUES (791, 'login', 'sessao', 1, 'admin', 1, 'admin', 'super_admin', '[]', '[]', '::1', '', '2026-03-20 13:19:28.99347-03');
INSERT INTO public.tb_auditoria VALUES (792, 'login', 'sessao', 23, 'renan', 23, 'renan', 'operador', '[]', '[]', '::1', '', '2026-03-20 13:19:29.154227-03');
INSERT INTO public.tb_auditoria VALUES (793, 'login', 'sessao', 1, 'admin', 1, 'admin', 'super_admin', '[]', '[]', '::1', '', '2026-03-20 13:19:33.578233-03');
INSERT INTO public.tb_auditoria VALUES (794, 'login', 'sessao', 23, 'renan', 23, 'renan', 'operador', '[]', '[]', '::1', '', '2026-03-20 13:19:33.775982-03');
INSERT INTO public.tb_auditoria VALUES (795, 'logout', 'sessao', NULL, NULL, 13, 'caio', 'desenvolvedor', '[]', '[]', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', '2026-03-20 13:19:53.453306-03');
INSERT INTO public.tb_auditoria VALUES (796, 'login', 'sessao', 13, 'caio', 13, 'caio', 'desenvolvedor', '[]', '[]', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', '2026-03-20 13:19:54.780895-03');
INSERT INTO public.tb_auditoria VALUES (797, 'login', 'sessao', 1, 'admin', 1, 'admin', 'super_admin', '[]', '[]', '::1', '', '2026-03-20 13:25:32.26383-03');
INSERT INTO public.tb_auditoria VALUES (798, 'login', 'sessao', 23, 'renan', 23, 'renan', 'operador', '[]', '[]', '::1', '', '2026-03-20 13:25:32.487986-03');
INSERT INTO public.tb_auditoria VALUES (799, 'login', 'sessao', 1, 'admin', 1, 'admin', 'super_admin', '[]', '[]', '::1', '', '2026-03-20 13:27:26.92598-03');
INSERT INTO public.tb_auditoria VALUES (800, 'login', 'sessao', 1, 'admin', 1, 'admin', 'super_admin', '[]', '[]', '::1', '', '2026-03-20 13:27:27.814887-03');
INSERT INTO public.tb_auditoria VALUES (801, 'login', 'sessao', 23, 'renan', 23, 'renan', 'operador', '[]', '[]', '::1', '', '2026-03-20 13:27:28.398016-03');
INSERT INTO public.tb_auditoria VALUES (802, 'login', 'sessao', 1, 'admin', 1, 'admin', 'super_admin', '[]', '[]', '::1', '', '2026-03-20 13:28:26.003141-03');
INSERT INTO public.tb_auditoria VALUES (803, 'criar', 'usuario', 102, 'rbac_admin', 1, 'admin', 'super_admin', '[]', '{"nivel_acesso": "admin"}', '::1', '', '2026-03-20 13:28:26.267251-03');
INSERT INTO public.tb_auditoria VALUES (804, 'criar', 'usuario', 103, 'rbac_dev', 1, 'admin', 'super_admin', '[]', '{"nivel_acesso": "desenvolvedor"}', '::1', '', '2026-03-20 13:28:26.455333-03');
INSERT INTO public.tb_auditoria VALUES (805, 'login', 'sessao', 1, 'admin', 1, 'admin', 'super_admin', '[]', '[]', '::1', '', '2026-03-20 13:28:26.664839-03');
INSERT INTO public.tb_auditoria VALUES (806, 'login', 'sessao', 102, 'rbac_admin', 102, 'rbac_admin', 'admin', '[]', '[]', '::1', '', '2026-03-20 13:28:26.886817-03');
INSERT INTO public.tb_auditoria VALUES (807, 'login', 'sessao', 103, 'rbac_dev', 103, 'rbac_dev', 'desenvolvedor', '[]', '[]', '::1', '', '2026-03-20 13:28:27.112722-03');
INSERT INTO public.tb_auditoria VALUES (808, 'login', 'sessao', 23, 'renan', 23, 'renan', 'operador', '[]', '[]', '::1', '', '2026-03-20 13:28:27.321713-03');
INSERT INTO public.tb_auditoria VALUES (809, 'excluir', 'usuario', 102, 'rbac_admin', 1, 'admin', 'super_admin', '[]', '[]', '::1', '', '2026-03-20 13:28:30.866591-03');
INSERT INTO public.tb_auditoria VALUES (810, 'excluir', 'usuario', 103, 'rbac_dev', 1, 'admin', 'super_admin', '[]', '[]', '::1', '', '2026-03-20 13:28:31.173156-03');
INSERT INTO public.tb_auditoria VALUES (811, 'logout', 'sessao', NULL, NULL, 13, 'caio', 'desenvolvedor', '[]', '[]', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', '2026-03-20 13:29:26.86636-03');
INSERT INTO public.tb_auditoria VALUES (812, 'login', 'sessao', 13, 'caio', 13, 'caio', 'desenvolvedor', '[]', '[]', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', '2026-03-20 13:29:28.403025-03');
INSERT INTO public.tb_auditoria VALUES (813, 'logout', 'sessao', NULL, NULL, 13, 'caio', 'desenvolvedor', '[]', '[]', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', '2026-03-20 13:29:33.575451-03');
INSERT INTO public.tb_auditoria VALUES (814, 'login', 'sessao', 13, 'caio', 13, 'caio', 'desenvolvedor', '[]', '[]', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', '2026-03-20 13:32:53.154305-03');
INSERT INTO public.tb_auditoria VALUES (815, 'login', 'sessao', 23, 'renan', 23, 'renan', 'operador', '[]', '[]', '::1', '', '2026-03-20 13:32:59.984934-03');
INSERT INTO public.tb_auditoria VALUES (816, 'login', 'sessao', 1, 'admin', 1, 'admin', 'super_admin', '[]', '[]', '::1', '', '2026-03-20 13:33:08.143115-03');
INSERT INTO public.tb_auditoria VALUES (817, 'login', 'sessao', 1, 'admin', 1, 'admin', 'super_admin', '[]', '[]', '::1', 'DMC-BrowserTest/2.0 (Simulated Browser)', '2026-03-20 13:33:15.226943-03');
INSERT INTO public.tb_auditoria VALUES (818, 'criar', 'empresa', 62, 'DMC Tecnologia', 1, 'admin', 'super_admin', '[]', '[]', '::1', 'DMC-BrowserTest/2.0 (Simulated Browser)', '2026-03-20 13:33:15.534295-03');
INSERT INTO public.tb_auditoria VALUES (819, 'criar', 'empresa', 63, 'Alpha Corp', 1, 'admin', 'super_admin', '[]', '[]', '::1', 'DMC-BrowserTest/2.0 (Simulated Browser)', '2026-03-20 13:33:15.73622-03');
INSERT INTO public.tb_auditoria VALUES (820, 'criar', 'empresa', 64, 'Beta Sistemas', 1, 'admin', 'super_admin', '[]', '[]', '::1', 'DMC-BrowserTest/2.0 (Simulated Browser)', '2026-03-20 13:33:15.891257-03');
INSERT INTO public.tb_auditoria VALUES (821, 'logout', 'sessao', NULL, NULL, 23, 'renan', 'operador', '[]', '[]', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-20 13:33:17.101933-03');
INSERT INTO public.tb_auditoria VALUES (822, 'criar', 'usuario', 104, 'admin_browser_test', 1, 'admin', 'super_admin', '[]', '{"nivel_acesso": "admin"}', '::1', 'DMC-BrowserTest/2.0 (Simulated Browser)', '2026-03-20 13:33:17.349875-03');
INSERT INTO public.tb_auditoria VALUES (823, 'criar', 'usuario', 105, 'dev_browser_test', 1, 'admin', 'super_admin', '[]', '{"nivel_acesso": "desenvolvedor"}', '::1', 'DMC-BrowserTest/2.0 (Simulated Browser)', '2026-03-20 13:33:17.647946-03');
INSERT INTO public.tb_auditoria VALUES (824, 'criar', 'usuario', 106, 'op_browser_test', 1, 'admin', 'super_admin', '[]', '{"nivel_acesso": "operador"}', '::1', 'DMC-BrowserTest/2.0 (Simulated Browser)', '2026-03-20 13:33:17.876703-03');
INSERT INTO public.tb_auditoria VALUES (825, 'login', 'sessao', 23, 'renan', 23, 'renan', 'operador', '[]', '[]', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-20 13:33:20.242053-03');
INSERT INTO public.tb_auditoria VALUES (826, 'criar', 'conexao', 88, '', 1, 'admin', 'super_admin', '[]', '[]', '::1', 'DMC-BrowserTest/2.0 (Simulated Browser)', '2026-03-20 13:33:25.125152-03');
INSERT INTO public.tb_auditoria VALUES (827, 'login', 'sessao', 104, 'admin_browser_test', 104, 'admin_browser_test', 'admin', '[]', '[]', '::1', 'DMC-BrowserTest/2.0 (Simulated Browser)', '2026-03-20 13:33:26.097047-03');
INSERT INTO public.tb_auditoria VALUES (828, 'criar', 'conexao', 89, '', 104, 'admin_browser_test', 'admin', '[]', '[]', '::1', 'DMC-BrowserTest/2.0 (Simulated Browser)', '2026-03-20 13:33:28.508918-03');
INSERT INTO public.tb_auditoria VALUES (829, 'login', 'sessao', 105, 'dev_browser_test', 105, 'dev_browser_test', 'desenvolvedor', '[]', '[]', '::1', 'DMC-BrowserTest/2.0 (Simulated Browser)', '2026-03-20 13:33:29.102998-03');
INSERT INTO public.tb_auditoria VALUES (830, 'criar', 'conexao', 90, '', 105, 'dev_browser_test', 'desenvolvedor', '[]', '[]', '::1', 'DMC-BrowserTest/2.0 (Simulated Browser)', '2026-03-20 13:33:30.983732-03');
INSERT INTO public.tb_auditoria VALUES (831, 'login', 'sessao', 106, 'op_browser_test', 106, 'op_browser_test', 'operador', '[]', '[]', '::1', 'DMC-BrowserTest/2.0 (Simulated Browser)', '2026-03-20 13:33:31.73464-03');
INSERT INTO public.tb_auditoria VALUES (832, 'excluir', 'conexao', 88, NULL, 1, 'admin', 'super_admin', '[]', '[]', '::1', 'DMC-BrowserTest/2.0 (Simulated Browser)', '2026-03-20 13:33:38.201558-03');
INSERT INTO public.tb_auditoria VALUES (833, 'excluir', 'conexao', 89, NULL, 1, 'admin', 'super_admin', '[]', '[]', '::1', 'DMC-BrowserTest/2.0 (Simulated Browser)', '2026-03-20 13:33:38.723624-03');
INSERT INTO public.tb_auditoria VALUES (834, 'excluir', 'conexao', 90, NULL, 1, 'admin', 'super_admin', '[]', '[]', '::1', 'DMC-BrowserTest/2.0 (Simulated Browser)', '2026-03-20 13:33:39.260764-03');
INSERT INTO public.tb_auditoria VALUES (835, 'excluir', 'usuario', 104, 'admin_browser_test', 1, 'admin', 'super_admin', '[]', '[]', '::1', 'DMC-BrowserTest/2.0 (Simulated Browser)', '2026-03-20 13:33:39.499069-03');
INSERT INTO public.tb_auditoria VALUES (836, 'excluir', 'usuario', 105, 'dev_browser_test', 1, 'admin', 'super_admin', '[]', '[]', '::1', 'DMC-BrowserTest/2.0 (Simulated Browser)', '2026-03-20 13:33:39.760519-03');
INSERT INTO public.tb_auditoria VALUES (837, 'excluir', 'usuario', 106, 'op_browser_test', 1, 'admin', 'super_admin', '[]', '[]', '::1', 'DMC-BrowserTest/2.0 (Simulated Browser)', '2026-03-20 13:33:40.121911-03');
INSERT INTO public.tb_auditoria VALUES (838, 'excluir', 'empresa', 62, 'DMC Tecnologia', 1, 'admin', 'super_admin', '[]', '[]', '::1', 'DMC-BrowserTest/2.0 (Simulated Browser)', '2026-03-20 13:33:41.166733-03');
INSERT INTO public.tb_auditoria VALUES (839, 'excluir', 'empresa', 63, 'Alpha Corp', 1, 'admin', 'super_admin', '[]', '[]', '::1', 'DMC-BrowserTest/2.0 (Simulated Browser)', '2026-03-20 13:33:41.310317-03');
INSERT INTO public.tb_auditoria VALUES (840, 'excluir', 'empresa', 64, 'Beta Sistemas', 1, 'admin', 'super_admin', '[]', '[]', '::1', 'DMC-BrowserTest/2.0 (Simulated Browser)', '2026-03-20 13:33:41.475376-03');
INSERT INTO public.tb_auditoria VALUES (841, 'logout', 'sessao', NULL, NULL, 23, 'renan', 'operador', '[]', '[]', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-20 13:36:09.341489-03');
INSERT INTO public.tb_auditoria VALUES (842, 'login', 'sessao', 23, 'renan', 23, 'renan', 'operador', '[]', '[]', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-20 13:36:13.495769-03');
INSERT INTO public.tb_auditoria VALUES (843, 'logout', 'sessao', NULL, NULL, 23, 'renan', 'operador', '[]', '[]', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-20 13:55:48.074888-03');
INSERT INTO public.tb_auditoria VALUES (844, 'login', 'sessao', 23, 'renan', 23, 'renan', 'operador', '[]', '[]', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-20 13:55:52.026762-03');
INSERT INTO public.tb_auditoria VALUES (845, 'editar', 'conexao', 15, '', 1, 'admin', 'super_admin', '[]', '[]', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-20 13:56:19.113555-03');
INSERT INTO public.tb_auditoria VALUES (846, 'editar', 'conexao', 14, '', 1, 'admin', 'super_admin', '[]', '[]', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-20 13:56:28.966908-03');
INSERT INTO public.tb_auditoria VALUES (847, 'login', 'sessao', 23, 'renan', 23, 'renan', 'operador', '[]', '[]', '::1', '', '2026-03-20 14:03:11.287845-03');
INSERT INTO public.tb_auditoria VALUES (848, 'logout', 'sessao', NULL, NULL, 1, 'admin', 'super_admin', '[]', '[]', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-20 14:07:45.16074-03');
INSERT INTO public.tb_auditoria VALUES (849, 'login', 'sessao', 23, 'renan', 23, 'renan', 'operador', '[]', '[]', '::1', '', '2026-03-20 14:12:52.580106-03');
INSERT INTO public.tb_auditoria VALUES (850, 'login', 'sessao', 3, 'leo', 3, 'leo', 'desenvolvedor', '[]', '[]', '::1', '', '2026-03-20 14:12:52.950491-03');
INSERT INTO public.tb_auditoria VALUES (851, 'login', 'sessao', 32, 'lucas', 32, 'lucas', 'admin', '[]', '[]', '::1', '', '2026-03-20 14:12:53.228965-03');
INSERT INTO public.tb_auditoria VALUES (852, 'login', 'sessao', 107, 'maria_infra', 107, 'maria_infra', 'desenvolvedor', '[]', '[]', '::1', '', '2026-03-20 14:12:53.431999-03');
INSERT INTO public.tb_auditoria VALUES (853, 'login', 'sessao', 108, 'joao_saude', 108, 'joao_saude', 'operador', '[]', '[]', '::1', '', '2026-03-20 14:12:53.640083-03');
INSERT INTO public.tb_auditoria VALUES (854, 'login', 'sessao', 110, 'pedro_agencia', 110, 'pedro_agencia', 'desenvolvedor', '[]', '[]', '::1', '', '2026-03-20 14:12:53.827057-03');
INSERT INTO public.tb_auditoria VALUES (855, 'login', 'sessao', 109, 'ana_multi', 109, 'ana_multi', 'admin', '[]', '[]', '::1', '', '2026-03-20 14:12:53.9674-03');
INSERT INTO public.tb_auditoria VALUES (856, 'login', 'sessao', 1, 'admin', 1, 'admin', 'super_admin', '[]', '[]', '::1', '', '2026-03-20 14:13:47.86742-03');
INSERT INTO public.tb_auditoria VALUES (857, 'login', 'sessao', 23, 'renan', 23, 'renan', 'operador', '[]', '[]', '::1', '', '2026-03-20 14:13:48.059461-03');
INSERT INTO public.tb_auditoria VALUES (858, 'login', 'sessao', 3, 'leo', 3, 'leo', 'desenvolvedor', '[]', '[]', '::1', '', '2026-03-20 14:13:48.242807-03');
INSERT INTO public.tb_auditoria VALUES (859, 'login', 'sessao', 32, 'lucas', 32, 'lucas', 'admin', '[]', '[]', '::1', '', '2026-03-20 14:13:48.413229-03');
INSERT INTO public.tb_auditoria VALUES (860, 'login', 'sessao', 107, 'maria_infra', 107, 'maria_infra', 'desenvolvedor', '[]', '[]', '::1', '', '2026-03-20 14:13:48.577833-03');
INSERT INTO public.tb_auditoria VALUES (861, 'login', 'sessao', 108, 'joao_saude', 108, 'joao_saude', 'operador', '[]', '[]', '::1', '', '2026-03-20 14:13:48.74254-03');
INSERT INTO public.tb_auditoria VALUES (862, 'login', 'sessao', 110, 'pedro_agencia', 110, 'pedro_agencia', 'desenvolvedor', '[]', '[]', '::1', '', '2026-03-20 14:13:48.909818-03');
INSERT INTO public.tb_auditoria VALUES (863, 'login', 'sessao', 109, 'ana_multi', 109, 'ana_multi', 'admin', '[]', '[]', '::1', '', '2026-03-20 14:13:49.072696-03');
INSERT INTO public.tb_auditoria VALUES (864, 'login', 'sessao', 1, 'admin', 1, 'admin', 'super_admin', '[]', '[]', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-20 14:14:06.427003-03');
INSERT INTO public.tb_auditoria VALUES (865, 'editar', 'conexao', 16, '', 1, 'admin', 'super_admin', '[]', '[]', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-20 14:15:56.128475-03');
INSERT INTO public.tb_auditoria VALUES (866, 'logout', 'sessao', NULL, NULL, 23, 'renan', 'operador', '[]', '[]', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-20 14:16:05.86298-03');
INSERT INTO public.tb_auditoria VALUES (867, 'login', 'sessao', 23, 'renan', 23, 'renan', 'operador', '[]', '[]', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-20 14:16:08.561747-03');
INSERT INTO public.tb_auditoria VALUES (868, 'editar', 'usuario', 23, 'renan', 1, 'admin', 'super_admin', '[]', '{"nivel_acesso": "operador"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-20 14:16:32.009964-03');
INSERT INTO public.tb_auditoria VALUES (869, 'editar', 'conexao', 15, '', 1, 'admin', 'super_admin', '[]', '[]', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-20 14:16:57.359151-03');
INSERT INTO public.tb_auditoria VALUES (870, 'editar', 'conexao', 15, '', 1, 'admin', 'super_admin', '[]', '[]', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-20 14:17:11.720403-03');
INSERT INTO public.tb_auditoria VALUES (871, 'editar', 'conexao', 15, '', 1, 'admin', 'super_admin', '[]', '[]', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-20 14:17:22.078744-03');
INSERT INTO public.tb_auditoria VALUES (872, 'logout', 'sessao', NULL, NULL, 23, 'renan', 'operador', '[]', '[]', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-20 14:17:31.199712-03');
INSERT INTO public.tb_auditoria VALUES (873, 'login', 'sessao', 23, 'renan', 23, 'renan', 'operador', '[]', '[]', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-20 14:17:36.114247-03');
INSERT INTO public.tb_auditoria VALUES (874, 'editar', 'rotina', 33, 'PG_Teste_02_Status_Chamados', 1, 'admin', 'super_admin', '[]', '[]', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-20 14:18:53.323508-03');
INSERT INTO public.tb_auditoria VALUES (875, 'logout', 'sessao', NULL, NULL, 23, 'renan', 'operador', '[]', '[]', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-20 14:19:02.798588-03');
INSERT INTO public.tb_auditoria VALUES (876, 'login', 'sessao', 23, 'renan', 23, 'renan', 'operador', '[]', '[]', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-20 14:19:05.374602-03');
INSERT INTO public.tb_auditoria VALUES (877, 'editar', 'rotina', 34, 'PG_Teste_03_Configuracoes', 1, 'admin', 'super_admin', '[]', '[]', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-20 14:19:57.671013-03');
INSERT INTO public.tb_auditoria VALUES (878, 'editar', 'usuario', 32, 'lucas', 1, 'admin', 'super_admin', '[]', '{"nivel_acesso": "admin"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-20 14:31:17.871685-03');
INSERT INTO public.tb_auditoria VALUES (879, 'editar', 'usuario', 32, 'lucas', 1, 'admin', 'super_admin', '[]', '{"nivel_acesso": "admin"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-20 14:31:27.145016-03');
INSERT INTO public.tb_auditoria VALUES (880, 'logout', 'sessao', NULL, NULL, 23, 'renan', 'operador', '[]', '[]', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-20 14:31:35.651088-03');
INSERT INTO public.tb_auditoria VALUES (881, 'login', 'sessao', 32, 'lucas', 32, 'lucas', 'admin', '[]', '[]', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-20 14:31:40.989105-03');
INSERT INTO public.tb_auditoria VALUES (882, 'editar', 'usuario', 32, 'lucas', 1, 'admin', 'super_admin', '[]', '{"nivel_acesso": "admin"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-20 14:32:04.368151-03');
INSERT INTO public.tb_auditoria VALUES (883, 'editar', 'usuario', 32, 'lucas', 1, 'admin', 'super_admin', '[]', '{"nivel_acesso": "admin"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-20 14:33:35.027462-03');
INSERT INTO public.tb_auditoria VALUES (1002, 'logout', 'sessao', NULL, NULL, 23, 'renan', 'operador', '[]', '[]', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-20 15:52:46.504115-03');
INSERT INTO public.tb_auditoria VALUES (884, 'editar', 'usuario', 32, 'lucas', 1, 'admin', 'super_admin', '[]', '{"nivel_acesso": "desenvolvedor"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-20 14:34:14.245455-03');
INSERT INTO public.tb_auditoria VALUES (885, 'logout', 'sessao', NULL, NULL, 32, 'lucas', 'admin', '[]', '[]', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-20 14:34:28.414684-03');
INSERT INTO public.tb_auditoria VALUES (886, 'login', 'sessao', 32, 'lucas', 32, 'lucas', 'desenvolvedor', '[]', '[]', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-20 14:34:31.942494-03');
INSERT INTO public.tb_auditoria VALUES (887, 'login', 'sessao', 23, 'renan', 23, 'renan', 'operador', '[]', '[]', '::1', '', '2026-03-20 14:35:40.071507-03');
INSERT INTO public.tb_auditoria VALUES (888, 'login', 'sessao', 1, 'admin', 1, 'admin', 'super_admin', '[]', '[]', '::1', '', '2026-03-20 14:40:08.732984-03');
INSERT INTO public.tb_auditoria VALUES (889, 'login', 'sessao', 23, 'renan', 23, 'renan', 'operador', '[]', '[]', '::1', '', '2026-03-20 14:40:09.313458-03');
INSERT INTO public.tb_auditoria VALUES (890, 'login', 'sessao', 3, 'leo', 3, 'leo', 'desenvolvedor', '[]', '[]', '::1', '', '2026-03-20 14:40:09.844578-03');
INSERT INTO public.tb_auditoria VALUES (891, 'login', 'sessao', 107, 'maria_infra', 107, 'maria_infra', 'desenvolvedor', '[]', '[]', '::1', '', '2026-03-20 14:40:10.604259-03');
INSERT INTO public.tb_auditoria VALUES (892, 'login', 'sessao', 108, 'joao_saude', 108, 'joao_saude', 'operador', '[]', '[]', '::1', '', '2026-03-20 14:40:11.104405-03');
INSERT INTO public.tb_auditoria VALUES (893, 'login', 'sessao', 110, 'pedro_agencia', 110, 'pedro_agencia', 'desenvolvedor', '[]', '[]', '::1', '', '2026-03-20 14:40:11.560326-03');
INSERT INTO public.tb_auditoria VALUES (894, 'login', 'sessao', 23, 'renan', 23, 'renan', 'operador', '[]', '[]', '::1', '', '2026-03-20 14:40:12.034228-03');
INSERT INTO public.tb_auditoria VALUES (895, 'login', 'sessao', 110, 'pedro_agencia', 110, 'pedro_agencia', 'desenvolvedor', '[]', '[]', '::1', '', '2026-03-20 14:40:12.279255-03');
INSERT INTO public.tb_auditoria VALUES (896, 'login', 'sessao', 1, 'admin', 1, 'admin', 'super_admin', '[]', '[]', '::1', '', '2026-03-20 14:40:44.906939-03');
INSERT INTO public.tb_auditoria VALUES (897, 'login', 'sessao', 23, 'renan', 23, 'renan', 'operador', '[]', '[]', '::1', '', '2026-03-20 14:40:45.716997-03');
INSERT INTO public.tb_auditoria VALUES (898, 'login', 'sessao', 3, 'leo', 3, 'leo', 'desenvolvedor', '[]', '[]', '::1', '', '2026-03-20 14:40:46.230879-03');
INSERT INTO public.tb_auditoria VALUES (899, 'login', 'sessao', 107, 'maria_infra', 107, 'maria_infra', 'desenvolvedor', '[]', '[]', '::1', '', '2026-03-20 14:40:46.953097-03');
INSERT INTO public.tb_auditoria VALUES (900, 'login', 'sessao', 108, 'joao_saude', 108, 'joao_saude', 'operador', '[]', '[]', '::1', '', '2026-03-20 14:40:47.412826-03');
INSERT INTO public.tb_auditoria VALUES (901, 'login', 'sessao', 110, 'pedro_agencia', 110, 'pedro_agencia', 'desenvolvedor', '[]', '[]', '::1', '', '2026-03-20 14:40:47.887505-03');
INSERT INTO public.tb_auditoria VALUES (902, 'login', 'sessao', 23, 'renan', 23, 'renan', 'operador', '[]', '[]', '::1', '', '2026-03-20 14:40:48.374287-03');
INSERT INTO public.tb_auditoria VALUES (903, 'login', 'sessao', 110, 'pedro_agencia', 110, 'pedro_agencia', 'desenvolvedor', '[]', '[]', '::1', '', '2026-03-20 14:40:48.559394-03');
INSERT INTO public.tb_auditoria VALUES (904, 'logout', 'sessao', NULL, NULL, 32, 'lucas', 'desenvolvedor', '[]', '[]', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-20 14:40:58.00463-03');
INSERT INTO public.tb_auditoria VALUES (905, 'login', 'sessao', 32, 'lucas', 32, 'lucas', 'desenvolvedor', '[]', '[]', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-20 14:41:05.853457-03');
INSERT INTO public.tb_auditoria VALUES (906, 'login', 'sessao', 1, 'admin', 1, 'admin', 'super_admin', '[]', '[]', '::1', '', '2026-03-20 14:41:46.352399-03');
INSERT INTO public.tb_auditoria VALUES (907, 'login', 'sessao', 23, 'renan', 23, 'renan', 'operador', '[]', '[]', '::1', '', '2026-03-20 14:41:46.860255-03');
INSERT INTO public.tb_auditoria VALUES (908, 'login', 'sessao', 3, 'leo', 3, 'leo', 'desenvolvedor', '[]', '[]', '::1', '', '2026-03-20 14:41:47.355309-03');
INSERT INTO public.tb_auditoria VALUES (909, 'login', 'sessao', 32, 'lucas', 32, 'lucas', 'admin', '[]', '[]', '::1', '', '2026-03-20 14:41:47.891809-03');
INSERT INTO public.tb_auditoria VALUES (910, 'login', 'sessao', 107, 'maria_infra', 107, 'maria_infra', 'desenvolvedor', '[]', '[]', '::1', '', '2026-03-20 14:41:48.408761-03');
INSERT INTO public.tb_auditoria VALUES (911, 'login', 'sessao', 108, 'joao_saude', 108, 'joao_saude', 'operador', '[]', '[]', '::1', '', '2026-03-20 14:41:48.938636-03');
INSERT INTO public.tb_auditoria VALUES (912, 'login', 'sessao', 110, 'pedro_agencia', 110, 'pedro_agencia', 'desenvolvedor', '[]', '[]', '::1', '', '2026-03-20 14:41:49.43883-03');
INSERT INTO public.tb_auditoria VALUES (913, 'login', 'sessao', 23, 'renan', 23, 'renan', 'operador', '[]', '[]', '::1', '', '2026-03-20 14:41:49.912304-03');
INSERT INTO public.tb_auditoria VALUES (914, 'login', 'sessao', 110, 'pedro_agencia', 110, 'pedro_agencia', 'desenvolvedor', '[]', '[]', '::1', '', '2026-03-20 14:41:50.119481-03');
INSERT INTO public.tb_auditoria VALUES (915, 'logout', 'sessao', NULL, NULL, 13, 'caio', 'desenvolvedor', '[]', '[]', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', '2026-03-20 14:42:18.584031-03');
INSERT INTO public.tb_auditoria VALUES (916, 'login', 'sessao', 23, 'renan', 23, 'renan', 'operador', '[]', '[]', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', '2026-03-20 14:42:22.09314-03');
INSERT INTO public.tb_auditoria VALUES (917, 'login', 'sessao', 1, 'admin', 1, 'admin', 'super_admin', '[]', '[]', '::1', '', '2026-03-20 14:42:42.004032-03');
INSERT INTO public.tb_auditoria VALUES (918, 'login', 'sessao', 23, 'renan', 23, 'renan', 'operador', '[]', '[]', '::1', '', '2026-03-20 14:42:42.521501-03');
INSERT INTO public.tb_auditoria VALUES (919, 'login', 'sessao', 3, 'leo', 3, 'leo', 'desenvolvedor', '[]', '[]', '::1', '', '2026-03-20 14:42:43.00807-03');
INSERT INTO public.tb_auditoria VALUES (920, 'login', 'sessao', 32, 'lucas', 32, 'lucas', 'admin', '[]', '[]', '::1', '', '2026-03-20 14:42:43.502953-03');
INSERT INTO public.tb_auditoria VALUES (921, 'login', 'sessao', 107, 'maria_infra', 107, 'maria_infra', 'desenvolvedor', '[]', '[]', '::1', '', '2026-03-20 14:42:44.059209-03');
INSERT INTO public.tb_auditoria VALUES (922, 'login', 'sessao', 108, 'joao_saude', 108, 'joao_saude', 'operador', '[]', '[]', '::1', '', '2026-03-20 14:42:44.536069-03');
INSERT INTO public.tb_auditoria VALUES (923, 'login', 'sessao', 110, 'pedro_agencia', 110, 'pedro_agencia', 'desenvolvedor', '[]', '[]', '::1', '', '2026-03-20 14:42:44.998387-03');
INSERT INTO public.tb_auditoria VALUES (924, 'login', 'sessao', 23, 'renan', 23, 'renan', 'operador', '[]', '[]', '::1', '', '2026-03-20 14:42:45.509287-03');
INSERT INTO public.tb_auditoria VALUES (925, 'login', 'sessao', 110, 'pedro_agencia', 110, 'pedro_agencia', 'desenvolvedor', '[]', '[]', '::1', '', '2026-03-20 14:42:45.702124-03');
INSERT INTO public.tb_auditoria VALUES (926, 'editar', 'usuario', 13, 'caio', 1, 'admin', 'super_admin', '[]', '{"nivel_acesso": "admin"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-20 14:43:52.054855-03');
INSERT INTO public.tb_auditoria VALUES (927, 'logout', 'sessao', NULL, NULL, 23, 'renan', 'operador', '[]', '[]', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', '2026-03-20 14:43:59.832979-03');
INSERT INTO public.tb_auditoria VALUES (928, 'login', 'sessao', 13, 'caio', 13, 'caio', 'admin', '[]', '[]', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', '2026-03-20 14:44:12.655843-03');
INSERT INTO public.tb_auditoria VALUES (929, 'login', 'sessao', 32, 'lucas', 32, 'lucas', 'desenvolvedor', '[]', '[]', '::1', '', '2026-03-20 14:56:54.595871-03');
INSERT INTO public.tb_auditoria VALUES (930, 'login', 'sessao', 1, 'admin', 1, 'admin', 'super_admin', '[]', '[]', '::1', '', '2026-03-20 14:56:57.695787-03');
INSERT INTO public.tb_auditoria VALUES (931, 'login', 'sessao', 3, 'leo', 3, 'leo', 'desenvolvedor', '[]', '[]', '::1', '', '2026-03-20 14:57:00.689641-03');
INSERT INTO public.tb_auditoria VALUES (932, 'login', 'sessao', 23, 'renan', 23, 'renan', 'operador', '[]', '[]', '::1', '', '2026-03-20 14:57:03.870809-03');
INSERT INTO public.tb_auditoria VALUES (933, 'login', 'sessao', 107, 'maria_infra', 107, 'maria_infra', 'desenvolvedor', '[]', '[]', '::1', '', '2026-03-20 14:57:05.909402-03');
INSERT INTO public.tb_auditoria VALUES (934, 'login', 'sessao', 108, 'joao_saude', 108, 'joao_saude', 'operador', '[]', '[]', '::1', '', '2026-03-20 14:57:09.975188-03');
INSERT INTO public.tb_auditoria VALUES (935, 'login', 'sessao', 110, 'pedro_agencia', 110, 'pedro_agencia', 'desenvolvedor', '[]', '[]', '::1', '', '2026-03-20 14:57:11.582504-03');
INSERT INTO public.tb_auditoria VALUES (936, 'logout', 'sessao', NULL, NULL, 32, 'lucas', 'desenvolvedor', '[]', '[]', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-20 14:58:58.723626-03');
INSERT INTO public.tb_auditoria VALUES (937, 'login', 'sessao', 32, 'lucas', 32, 'lucas', 'desenvolvedor', '[]', '[]', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-20 14:59:03.055233-03');
INSERT INTO public.tb_auditoria VALUES (938, 'login', 'sessao', 32, 'lucas', 32, 'lucas', 'desenvolvedor', '[]', '[]', '::1', '', '2026-03-20 15:02:29.672084-03');
INSERT INTO public.tb_auditoria VALUES (939, 'login', 'sessao', 1, 'admin', 1, 'admin', 'super_admin', '[]', '[]', '::1', '', '2026-03-20 15:02:29.977148-03');
INSERT INTO public.tb_auditoria VALUES (940, 'login', 'sessao', 3, 'leo', 3, 'leo', 'desenvolvedor', '[]', '[]', '::1', '', '2026-03-20 15:02:30.386276-03');
INSERT INTO public.tb_auditoria VALUES (941, 'editar', 'usuario', 32, 'lucas', 1, 'admin', 'super_admin', '[]', '{"nivel_acesso": "desenvolvedor"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-20 15:16:56.234789-03');
INSERT INTO public.tb_auditoria VALUES (942, 'editar', 'usuario', 32, 'lucas', 1, 'admin', 'super_admin', '[]', '{"nivel_acesso": "desenvolvedor"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-20 15:17:13.663084-03');
INSERT INTO public.tb_auditoria VALUES (943, 'login', 'sessao', 32, 'lucas', 32, 'lucas', 'desenvolvedor', '[]', '[]', '::1', '', '2026-03-20 15:27:29.440778-03');
INSERT INTO public.tb_auditoria VALUES (944, 'login', 'sessao', 1, 'admin', 1, 'admin', 'super_admin', '[]', '[]', '::1', '', '2026-03-20 15:27:32.055055-03');
INSERT INTO public.tb_auditoria VALUES (945, 'login', 'sessao', 3, 'leo', 3, 'leo', 'desenvolvedor', '[]', '[]', '::1', '', '2026-03-20 15:27:34.448644-03');
INSERT INTO public.tb_auditoria VALUES (946, 'login', 'sessao', 23, 'renan', 23, 'renan', 'operador', '[]', '[]', '::1', '', '2026-03-20 15:27:37.661522-03');
INSERT INTO public.tb_auditoria VALUES (947, 'login', 'sessao', 107, 'maria_infra', 107, 'maria_infra', 'desenvolvedor', '[]', '[]', '::1', '', '2026-03-20 15:27:40.428425-03');
INSERT INTO public.tb_auditoria VALUES (948, 'login', 'sessao', 108, 'joao_saude', 108, 'joao_saude', 'operador', '[]', '[]', '::1', '', '2026-03-20 15:27:43.537831-03');
INSERT INTO public.tb_auditoria VALUES (949, 'login', 'sessao', 110, 'pedro_agencia', 110, 'pedro_agencia', 'desenvolvedor', '[]', '[]', '::1', '', '2026-03-20 15:27:45.14949-03');
INSERT INTO public.tb_auditoria VALUES (950, 'logout', 'sessao', NULL, NULL, 32, 'lucas', 'desenvolvedor', '[]', '[]', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-20 15:28:16.510358-03');
INSERT INTO public.tb_auditoria VALUES (951, 'login', 'sessao', 32, 'lucas', 32, 'lucas', 'desenvolvedor', '[]', '[]', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-20 15:28:23.438325-03');
INSERT INTO public.tb_auditoria VALUES (952, 'editar', 'usuario', 32, 'lucas', 1, 'admin', 'super_admin', '[]', '{"nivel_acesso": "desenvolvedor"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-20 15:33:34.368421-03');
INSERT INTO public.tb_auditoria VALUES (953, 'login', 'sessao', 32, 'lucas', 32, 'lucas', 'desenvolvedor', '[]', '[]', '::1', '', '2026-03-20 15:34:44.358137-03');
INSERT INTO public.tb_auditoria VALUES (954, 'login', 'sessao', 1, 'admin', 1, 'admin', 'super_admin', '[]', '[]', '::1', '', '2026-03-20 15:34:44.715017-03');
INSERT INTO public.tb_auditoria VALUES (955, 'login', 'sessao', 3, 'leo', 3, 'leo', 'desenvolvedor', '[]', '[]', '::1', '', '2026-03-20 15:34:45.11441-03');
INSERT INTO public.tb_auditoria VALUES (956, 'login', 'sessao', 32, 'lucas', 32, 'lucas', 'desenvolvedor', '[]', '[]', '::1', '', '2026-03-20 15:35:51.915865-03');
INSERT INTO public.tb_auditoria VALUES (957, 'login', 'sessao', 1, 'admin', 1, 'admin', 'super_admin', '[]', '[]', '::1', '', '2026-03-20 15:35:52.168934-03');
INSERT INTO public.tb_auditoria VALUES (958, 'login', 'sessao', 3, 'leo', 3, 'leo', 'desenvolvedor', '[]', '[]', '::1', '', '2026-03-20 15:35:52.430363-03');
INSERT INTO public.tb_auditoria VALUES (959, 'login', 'sessao', 32, 'lucas', 32, 'lucas', 'desenvolvedor', '[]', '[]', '::1', '', '2026-03-20 15:35:58.788408-03');
INSERT INTO public.tb_auditoria VALUES (960, 'login', 'sessao', 1, 'admin', 1, 'admin', 'super_admin', '[]', '[]', '::1', '', '2026-03-20 15:36:00.210212-03');
INSERT INTO public.tb_auditoria VALUES (961, 'login', 'sessao', 3, 'leo', 3, 'leo', 'desenvolvedor', '[]', '[]', '::1', '', '2026-03-20 15:36:02.306582-03');
INSERT INTO public.tb_auditoria VALUES (962, 'login', 'sessao', 23, 'renan', 23, 'renan', 'operador', '[]', '[]', '::1', '', '2026-03-20 15:36:04.262042-03');
INSERT INTO public.tb_auditoria VALUES (963, 'login', 'sessao', 107, 'maria_infra', 107, 'maria_infra', 'desenvolvedor', '[]', '[]', '::1', '', '2026-03-20 15:36:05.439617-03');
INSERT INTO public.tb_auditoria VALUES (964, 'login', 'sessao', 108, 'joao_saude', 108, 'joao_saude', 'operador', '[]', '[]', '::1', '', '2026-03-20 15:36:07.666362-03');
INSERT INTO public.tb_auditoria VALUES (965, 'login', 'sessao', 110, 'pedro_agencia', 110, 'pedro_agencia', 'desenvolvedor', '[]', '[]', '::1', '', '2026-03-20 15:36:08.917183-03');
INSERT INTO public.tb_auditoria VALUES (966, 'criar', 'conexao', 94, '', 32, 'lucas', 'desenvolvedor', '[]', '[]', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-20 15:38:39.181914-03');
INSERT INTO public.tb_auditoria VALUES (967, 'logout', 'sessao', NULL, NULL, 32, 'lucas', 'desenvolvedor', '[]', '[]', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-20 15:39:12.654946-03');
INSERT INTO public.tb_auditoria VALUES (968, 'login', 'sessao', 23, 'renan', 23, 'renan', 'operador', '[]', '[]', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-20 15:39:15.881854-03');
INSERT INTO public.tb_auditoria VALUES (969, 'editar', 'usuario', 23, 'renan', 1, 'admin', 'super_admin', '[]', '{"nivel_acesso": "operador"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-20 15:42:03.373667-03');
INSERT INTO public.tb_auditoria VALUES (970, 'editar', 'usuario', 23, 'renan', 1, 'admin', 'super_admin', '[]', '{"nivel_acesso": "operador"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-20 15:42:23.359409-03');
INSERT INTO public.tb_auditoria VALUES (971, 'logout', 'sessao', NULL, NULL, 23, 'renan', 'operador', '[]', '[]', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-20 15:42:37.214871-03');
INSERT INTO public.tb_auditoria VALUES (972, 'login', 'sessao', 23, 'renan', 23, 'renan', 'operador', '[]', '[]', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-20 15:42:42.837997-03');
INSERT INTO public.tb_auditoria VALUES (973, 'login', 'sessao', 1, 'admin', 1, 'admin', 'super_admin', '[]', '[]', '::1', '', '2026-03-20 15:43:34.108701-03');
INSERT INTO public.tb_auditoria VALUES (974, 'login', 'sessao', 23, 'renan', 23, 'renan', 'operador', '[]', '[]', '::1', '', '2026-03-20 15:43:34.732504-03');
INSERT INTO public.tb_auditoria VALUES (975, 'login', 'sessao', 32, 'lucas', 32, 'lucas', 'desenvolvedor', '[]', '[]', '::1', '', '2026-03-20 15:43:35.403479-03');
INSERT INTO public.tb_auditoria VALUES (976, 'login', 'sessao', 1, 'admin', 1, 'admin', 'super_admin', '[]', '[]', '::1', '', '2026-03-20 15:44:33.736833-03');
INSERT INTO public.tb_auditoria VALUES (977, 'login', 'sessao', 23, 'renan', 23, 'renan', 'operador', '[]', '[]', '::1', '', '2026-03-20 15:44:34.354421-03');
INSERT INTO public.tb_auditoria VALUES (978, 'login', 'sessao', 32, 'lucas', 32, 'lucas', 'desenvolvedor', '[]', '[]', '::1', '', '2026-03-20 15:44:34.93574-03');
INSERT INTO public.tb_auditoria VALUES (979, 'login', 'sessao', 32, 'lucas', 32, 'lucas', 'desenvolvedor', '[]', '[]', '::1', '', '2026-03-20 15:44:47.24842-03');
INSERT INTO public.tb_auditoria VALUES (980, 'login', 'sessao', 1, 'admin', 1, 'admin', 'super_admin', '[]', '[]', '::1', '', '2026-03-20 15:44:49.613961-03');
INSERT INTO public.tb_auditoria VALUES (981, 'login', 'sessao', 3, 'leo', 3, 'leo', 'desenvolvedor', '[]', '[]', '::1', '', '2026-03-20 15:44:52.02555-03');
INSERT INTO public.tb_auditoria VALUES (982, 'login', 'sessao', 23, 'renan', 23, 'renan', 'operador', '[]', '[]', '::1', '', '2026-03-20 15:44:54.276928-03');
INSERT INTO public.tb_auditoria VALUES (983, 'login', 'sessao', 107, 'maria_infra', 107, 'maria_infra', 'desenvolvedor', '[]', '[]', '::1', '', '2026-03-20 15:44:56.118617-03');
INSERT INTO public.tb_auditoria VALUES (984, 'login', 'sessao', 108, 'joao_saude', 108, 'joao_saude', 'operador', '[]', '[]', '::1', '', '2026-03-20 15:44:58.947455-03');
INSERT INTO public.tb_auditoria VALUES (985, 'login', 'sessao', 110, 'pedro_agencia', 110, 'pedro_agencia', 'desenvolvedor', '[]', '[]', '::1', '', '2026-03-20 15:45:00.345988-03');
INSERT INTO public.tb_auditoria VALUES (986, 'login', 'sessao', 23, 'renan', 23, 'renan', 'operador', '[]', '[]', '::1', '', '2026-03-20 15:48:01.985779-03');
INSERT INTO public.tb_auditoria VALUES (987, 'login', 'sessao', 23, 'renan', 23, 'renan', 'operador', '[]', '[]', '::1', '', '2026-03-20 15:50:39.590969-03');
INSERT INTO public.tb_auditoria VALUES (988, 'login', 'sessao', 32, 'lucas', 32, 'lucas', 'desenvolvedor', '[]', '[]', '::1', '', '2026-03-20 15:50:52.627331-03');
INSERT INTO public.tb_auditoria VALUES (989, 'login', 'sessao', 1, 'admin', 1, 'admin', 'super_admin', '[]', '[]', '::1', '', '2026-03-20 15:50:55.279203-03');
INSERT INTO public.tb_auditoria VALUES (990, 'login', 'sessao', 3, 'leo', 3, 'leo', 'desenvolvedor', '[]', '[]', '::1', '', '2026-03-20 15:50:57.681251-03');
INSERT INTO public.tb_auditoria VALUES (991, 'login', 'sessao', 23, 'renan', 23, 'renan', 'operador', '[]', '[]', '::1', '', '2026-03-20 15:51:00.033249-03');
INSERT INTO public.tb_auditoria VALUES (992, 'login', 'sessao', 107, 'maria_infra', 107, 'maria_infra', 'desenvolvedor', '[]', '[]', '::1', '', '2026-03-20 15:51:01.798555-03');
INSERT INTO public.tb_auditoria VALUES (993, 'login', 'sessao', 108, 'joao_saude', 108, 'joao_saude', 'operador', '[]', '[]', '::1', '', '2026-03-20 15:51:04.214223-03');
INSERT INTO public.tb_auditoria VALUES (994, 'login', 'sessao', 110, 'pedro_agencia', 110, 'pedro_agencia', 'desenvolvedor', '[]', '[]', '::1', '', '2026-03-20 15:51:05.979695-03');
INSERT INTO public.tb_auditoria VALUES (995, 'login', 'sessao', 32, 'lucas', 32, 'lucas', 'desenvolvedor', '[]', '[]', '::1', '', '2026-03-20 15:51:55.598981-03');
INSERT INTO public.tb_auditoria VALUES (996, 'login', 'sessao', 1, 'admin', 1, 'admin', 'super_admin', '[]', '[]', '::1', '', '2026-03-20 15:51:58.550981-03');
INSERT INTO public.tb_auditoria VALUES (997, 'login', 'sessao', 3, 'leo', 3, 'leo', 'desenvolvedor', '[]', '[]', '::1', '', '2026-03-20 15:52:02.664225-03');
INSERT INTO public.tb_auditoria VALUES (998, 'login', 'sessao', 23, 'renan', 23, 'renan', 'operador', '[]', '[]', '::1', '', '2026-03-20 15:52:05.009689-03');
INSERT INTO public.tb_auditoria VALUES (999, 'login', 'sessao', 107, 'maria_infra', 107, 'maria_infra', 'desenvolvedor', '[]', '[]', '::1', '', '2026-03-20 15:52:06.643005-03');
INSERT INTO public.tb_auditoria VALUES (1000, 'login', 'sessao', 108, 'joao_saude', 108, 'joao_saude', 'operador', '[]', '[]', '::1', '', '2026-03-20 15:52:09.700673-03');
INSERT INTO public.tb_auditoria VALUES (1001, 'login', 'sessao', 110, 'pedro_agencia', 110, 'pedro_agencia', 'desenvolvedor', '[]', '[]', '::1', '', '2026-03-20 15:52:11.681667-03');
INSERT INTO public.tb_auditoria VALUES (1003, 'login', 'sessao', 23, 'renan', 23, 'renan', 'operador', '[]', '[]', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-20 15:52:49.817904-03');
INSERT INTO public.tb_auditoria VALUES (1004, 'logout', 'sessao', NULL, NULL, 1, 'admin', 'super_admin', '[]', '[]', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-20 15:54:10.514668-03');
INSERT INTO public.tb_auditoria VALUES (1005, 'login', 'sessao', 32, 'lucas', 32, 'lucas', 'desenvolvedor', '[]', '[]', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-20 15:54:21.188125-03');
INSERT INTO public.tb_auditoria VALUES (1006, 'logout', 'sessao', NULL, NULL, 32, 'lucas', 'desenvolvedor', '[]', '[]', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-20 16:02:20.120534-03');
INSERT INTO public.tb_auditoria VALUES (1007, 'login', 'sessao', 1, 'admin', 1, 'admin', 'super_admin', '[]', '[]', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-20 16:02:26.103902-03');
INSERT INTO public.tb_auditoria VALUES (1008, 'editar', 'usuario', 23, 'renan', 1, 'admin', 'super_admin', '[]', '{"nivel_acesso": "operador"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-20 16:02:44.480155-03');
INSERT INTO public.tb_auditoria VALUES (1009, 'login', 'sessao', 23, 'renan', 23, 'renan', 'operador', '[]', '[]', '::1', '', '2026-03-20 16:04:20.485279-03');
INSERT INTO public.tb_auditoria VALUES (1010, 'login', 'sessao', 23, 'renan', 23, 'renan', 'operador', '[]', '[]', '::1', '', '2026-03-20 16:04:45.258822-03');
INSERT INTO public.tb_auditoria VALUES (1011, 'login', 'sessao', 1, 'admin', 1, 'admin', 'super_admin', '[]', '[]', '::1', '', '2026-03-20 16:05:14.933672-03');
INSERT INTO public.tb_auditoria VALUES (1012, 'login', 'sessao', 1, 'admin', 1, 'admin', 'super_admin', '[]', '[]', '::1', '', '2026-03-20 16:05:34.50636-03');
INSERT INTO public.tb_auditoria VALUES (1013, 'login', 'sessao', 32, 'lucas', 32, 'lucas', 'desenvolvedor', '[]', '[]', '::1', '', '2026-03-20 16:05:41.493239-03');
INSERT INTO public.tb_auditoria VALUES (1014, 'login', 'sessao', 1, 'admin', 1, 'admin', 'super_admin', '[]', '[]', '::1', '', '2026-03-20 16:05:43.750296-03');
INSERT INTO public.tb_auditoria VALUES (1015, 'login', 'sessao', 3, 'leo', 3, 'leo', 'desenvolvedor', '[]', '[]', '::1', '', '2026-03-20 16:05:46.180998-03');
INSERT INTO public.tb_auditoria VALUES (1016, 'login', 'sessao', 23, 'renan', 23, 'renan', 'operador', '[]', '[]', '::1', '', '2026-03-20 16:05:49.114542-03');
INSERT INTO public.tb_auditoria VALUES (1017, 'login', 'sessao', 107, 'maria_infra', 107, 'maria_infra', 'desenvolvedor', '[]', '[]', '::1', '', '2026-03-20 16:05:50.716074-03');
INSERT INTO public.tb_auditoria VALUES (1018, 'login', 'sessao', 108, 'joao_saude', 108, 'joao_saude', 'operador', '[]', '[]', '::1', '', '2026-03-20 16:05:53.3894-03');
INSERT INTO public.tb_auditoria VALUES (1019, 'login', 'sessao', 110, 'pedro_agencia', 110, 'pedro_agencia', 'desenvolvedor', '[]', '[]', '::1', '', '2026-03-20 16:05:55.526744-03');
INSERT INTO public.tb_auditoria VALUES (1020, 'login', 'sessao', 23, 'renan', 23, 'renan', 'operador', '[]', '[]', '::1', '', '2026-03-20 16:08:04.560439-03');
INSERT INTO public.tb_auditoria VALUES (1021, 'login', 'sessao', 1, 'admin', 1, 'admin', 'super_admin', '[]', '[]', '::1', '', '2026-03-20 16:08:04.788221-03');
INSERT INTO public.tb_auditoria VALUES (1022, 'editar', 'usuario', 32, 'lucas', 1, 'admin', 'super_admin', '[]', '{"nivel_acesso": "desenvolvedor"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-20 16:08:23.257114-03');
INSERT INTO public.tb_auditoria VALUES (1023, 'logout', 'sessao', NULL, NULL, 1, 'admin', 'super_admin', '[]', '[]', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-20 16:08:25.945622-03');
INSERT INTO public.tb_auditoria VALUES (1024, 'login', 'sessao', 23, 'renan', 23, 'renan', 'operador', '[]', '[]', '::1', '', '2026-03-20 16:08:27.423673-03');
INSERT INTO public.tb_auditoria VALUES (1025, 'login', 'sessao', 1, 'admin', 1, 'admin', 'super_admin', '[]', '[]', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-20 16:08:36.260014-03');
INSERT INTO public.tb_auditoria VALUES (1026, 'excluir', 'conexao', 94, NULL, 1, 'admin', 'super_admin', '[]', '[]', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-20 16:08:41.926005-03');
INSERT INTO public.tb_auditoria VALUES (1027, 'logout', 'sessao', NULL, NULL, 1, 'admin', 'super_admin', '[]', '[]', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-20 16:08:44.641909-03');
INSERT INTO public.tb_auditoria VALUES (1028, 'login', 'sessao', 32, 'lucas', 32, 'lucas', 'desenvolvedor', '[]', '[]', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-20 16:08:50.148893-03');
INSERT INTO public.tb_auditoria VALUES (1029, 'logout', 'sessao', NULL, NULL, 32, 'lucas', 'desenvolvedor', '[]', '[]', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-20 16:16:49.162264-03');
INSERT INTO public.tb_auditoria VALUES (1030, 'login', 'sessao', 1, 'admin', 1, 'admin', 'super_admin', '[]', '[]', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-20 16:16:53.409253-03');
INSERT INTO public.tb_auditoria VALUES (1031, 'login', 'sessao', 1, 'admin', 1, 'admin', 'super_admin', '[]', '[]', '::1', '', '2026-03-20 16:23:15.559824-03');
INSERT INTO public.tb_auditoria VALUES (1032, 'login', 'sessao', 32, 'lucas', 32, 'lucas', 'desenvolvedor', '[]', '[]', '::1', '', '2026-03-20 16:23:15.830454-03');
INSERT INTO public.tb_auditoria VALUES (1033, 'login', 'sessao', 23, 'renan', 23, 'renan', 'operador', '[]', '[]', '::1', '', '2026-03-20 16:23:16.171719-03');
INSERT INTO public.tb_auditoria VALUES (1034, 'login', 'sessao', 1, 'admin', 1, 'admin', 'super_admin', '[]', '[]', '::1', '', '2026-03-20 16:24:06.723518-03');
INSERT INTO public.tb_auditoria VALUES (1035, 'login', 'sessao', 32, 'lucas', 32, 'lucas', 'desenvolvedor', '[]', '[]', '::1', '', '2026-03-20 16:24:07.050972-03');
INSERT INTO public.tb_auditoria VALUES (1036, 'login', 'sessao', 23, 'renan', 23, 'renan', 'operador', '[]', '[]', '::1', '', '2026-03-20 16:24:07.448851-03');
INSERT INTO public.tb_auditoria VALUES (1037, 'login', 'sessao', 32, 'lucas', 32, 'lucas', 'desenvolvedor', '[]', '[]', '::1', '', '2026-03-20 16:24:14.351866-03');
INSERT INTO public.tb_auditoria VALUES (1038, 'login', 'sessao', 1, 'admin', 1, 'admin', 'super_admin', '[]', '[]', '::1', '', '2026-03-20 16:24:16.664282-03');
INSERT INTO public.tb_auditoria VALUES (1039, 'login', 'sessao', 3, 'leo', 3, 'leo', 'desenvolvedor', '[]', '[]', '::1', '', '2026-03-20 16:24:18.788511-03');
INSERT INTO public.tb_auditoria VALUES (1040, 'login', 'sessao', 23, 'renan', 23, 'renan', 'operador', '[]', '[]', '::1', '', '2026-03-20 16:24:21.004485-03');
INSERT INTO public.tb_auditoria VALUES (1041, 'login', 'sessao', 107, 'maria_infra', 107, 'maria_infra', 'desenvolvedor', '[]', '[]', '::1', '', '2026-03-20 16:24:22.534283-03');
INSERT INTO public.tb_auditoria VALUES (1042, 'login', 'sessao', 108, 'joao_saude', 108, 'joao_saude', 'operador', '[]', '[]', '::1', '', '2026-03-20 16:24:25.009426-03');
INSERT INTO public.tb_auditoria VALUES (1043, 'login', 'sessao', 110, 'pedro_agencia', 110, 'pedro_agencia', 'desenvolvedor', '[]', '[]', '::1', '', '2026-03-20 16:24:26.271365-03');
INSERT INTO public.tb_auditoria VALUES (1044, 'editar', 'configuracao', NULL, 'Grupo: email', 1, 'admin', 'super_admin', '{"smtp_host": "", "smtp_port": "587", "smtp_user": "", "smtp_password": "", "smtp_from_name": "DMC DataLoad", "smtp_encryption": "tls", "smtp_from_email": ""}', '{"smtp_host": "smtp.hostinger.com", "smtp_port": "587", "smtp_user": "dmc@dynamicmotioncentury.com.br", "smtp_password": "****", "smtp_from_name": "DMC DataLoad", "smtp_encryption": "tls", "smtp_from_email": "dmc@dynamicmotioncentury.com.br"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-20 16:27:36.489668-03');
INSERT INTO public.tb_auditoria VALUES (1045, 'login', 'sessao', 32, 'lucas', 32, 'lucas', 'desenvolvedor', '[]', '[]', '::1', '', '2026-03-20 16:29:53.703312-03');
INSERT INTO public.tb_auditoria VALUES (1046, 'login', 'sessao', 1, 'admin', 1, 'admin', 'super_admin', '[]', '[]', '::1', '', '2026-03-20 16:29:56.053222-03');
INSERT INTO public.tb_auditoria VALUES (1047, 'login', 'sessao', 3, 'leo', 3, 'leo', 'desenvolvedor', '[]', '[]', '::1', '', '2026-03-20 16:29:58.664075-03');
INSERT INTO public.tb_auditoria VALUES (1048, 'login', 'sessao', 23, 'renan', 23, 'renan', 'operador', '[]', '[]', '::1', '', '2026-03-20 16:30:01.782828-03');
INSERT INTO public.tb_auditoria VALUES (1049, 'login', 'sessao', 107, 'maria_infra', 107, 'maria_infra', 'desenvolvedor', '[]', '[]', '::1', '', '2026-03-20 16:30:03.121288-03');
INSERT INTO public.tb_auditoria VALUES (1050, 'login', 'sessao', 108, 'joao_saude', 108, 'joao_saude', 'operador', '[]', '[]', '::1', '', '2026-03-20 16:30:05.26142-03');
INSERT INTO public.tb_auditoria VALUES (1051, 'login', 'sessao', 110, 'pedro_agencia', 110, 'pedro_agencia', 'desenvolvedor', '[]', '[]', '::1', '', '2026-03-20 16:30:06.606203-03');
INSERT INTO public.tb_auditoria VALUES (1052, 'login', 'sessao', 1, 'admin', 1, 'admin', 'super_admin', '[]', '[]', '::1', '', '2026-03-20 16:35:23.28096-03');
INSERT INTO public.tb_auditoria VALUES (1053, 'editar', 'configuracao', NULL, 'Grupo: geral', 1, 'admin', 'super_admin', '{"app_url": false, "app_nome": "DMC DataLoad", "app_idioma": "pt-BR", "app_timezone": "America/Sao_Paulo"}', '{"app_url": "http://localhost/DMC-DATALOAD/public", "app_nome": "DMC DataLoad Test", "app_idioma": "pt_BR", "app_timezone": "America/Sao_Paulo"}', '::1', '', '2026-03-20 16:35:23.489949-03');
INSERT INTO public.tb_auditoria VALUES (1054, 'editar', 'configuracao', NULL, 'Grupo: geral', 1, 'admin', 'super_admin', '{"app_manutencao": "0"}', '{"app_manutencao": "1"}', '::1', '', '2026-03-20 16:35:23.735945-03');
INSERT INTO public.tb_auditoria VALUES (1055, 'editar', 'configuracao', NULL, 'Grupo: geral', 1, 'admin', 'super_admin', '{"app_manutencao": "1"}', '{"app_manutencao": "0"}', '::1', '', '2026-03-20 16:35:23.967398-03');
INSERT INTO public.tb_auditoria VALUES (1056, 'editar', 'configuracao', NULL, 'Grupo: ldap', 1, 'admin', 'super_admin', '{"ldap_ssl": false, "ldap_host": false, "ldap_port": false, "ldap_filtro": false, "ldap_base_dn": false, "ldap_bind_dn": false, "ldap_bind_password": false}', '{"ldap_ssl": "0", "ldap_host": "ldap.test.local", "ldap_port": "389", "ldap_filtro": "(sAMAccountName={username})", "ldap_base_dn": "dc=test,dc=local", "ldap_bind_dn": "cn=admin,dc=test,dc=local", "ldap_bind_password": "testpass123"}', '::1', '', '2026-03-20 16:35:24.096096-03');
INSERT INTO public.tb_auditoria VALUES (1057, 'editar', 'configuracao', NULL, 'Grupo: scheduler', 1, 'admin', 'super_admin', '{"scheduler_retry": false, "scheduler_timeout": "3600", "scheduler_intervalo": "60", "scheduler_max_paralelo": "5"}', '{"scheduler_retry": "1", "scheduler_timeout": "120", "scheduler_intervalo": "30", "scheduler_max_paralelo": "3"}', '::1', '', '2026-03-20 16:35:24.373304-03');
INSERT INTO public.tb_auditoria VALUES (1058, 'editar', 'configuracao', NULL, 'Grupo: seguranca', 1, 'admin', 'super_admin', '{"seguranca_tempo_bloqueio": "900", "seguranca_timeout_sessao": "3600", "seguranca_tentativas_login": "5"}', '{"seguranca_tempo_bloqueio": "15", "seguranca_timeout_sessao": "120", "seguranca_tentativas_login": "5"}', '::1', '', '2026-03-20 16:35:24.628764-03');
INSERT INTO public.tb_auditoria VALUES (1059, 'editar', 'configuracao', NULL, 'Grupo: notificacoes', 1, 'admin', 'super_admin', '{"notif_email_falha": "1", "notif_webhook_url": "", "notif_email_sucesso": false, "notif_webhook_ativo": "0"}', '{"notif_email_falha": "1", "notif_webhook_url": "", "notif_email_sucesso": "0", "notif_webhook_ativo": "0"}', '::1', '', '2026-03-20 16:35:24.882381-03');
INSERT INTO public.tb_auditoria VALUES (1060, 'editar', 'configuracao', NULL, 'Grupo: geral', 1, 'admin', 'super_admin', '{"app_nome": "DMC DataLoad Test"}', '{"app_nome": "DMC DataLoad"}', '::1', '', '2026-03-20 16:35:25.429323-03');
INSERT INTO public.tb_auditoria VALUES (1061, 'login', 'sessao', 1, 'admin', 1, 'admin', 'super_admin', '[]', '[]', '::1', '', '2026-03-20 16:41:22.629992-03');
INSERT INTO public.tb_auditoria VALUES (1062, 'editar', 'configuracao', NULL, 'Grupo: geral', 1, 'admin', 'super_admin', '{"app_url": "http://localhost/DMC-DATALOAD/public", "app_nome": "DMC DataLoad", "app_idioma": "pt_BR", "app_timezone": "America/Sao_Paulo", "app_descricao": false, "modo_manutencao": false}', '{"app_url": "http://localhost/DMC-DATALOAD/public", "app_nome": "DMC DataLoad Test V2", "app_idioma": "pt_BR", "app_timezone": "America/Sao_Paulo", "app_descricao": "Descrição teste v2", "modo_manutencao": "0"}', '::1', '', '2026-03-20 16:41:22.84885-03');
INSERT INTO public.tb_auditoria VALUES (1063, 'editar', 'configuracao', NULL, 'Grupo: geral', 1, 'admin', 'super_admin', '{"app_nome": "DMC DataLoad Test V2"}', '{"app_nome": "DMC DataLoad"}', '::1', '', '2026-03-20 16:41:23.109501-03');
INSERT INTO public.tb_auditoria VALUES (1064, 'editar', 'configuracao', NULL, 'Grupo: ldap', 1, 'admin', 'super_admin', '{"ldap_ssl": "0", "ldap_host": "ldap.test.local", "ldap_port": "389", "ldap_ativo": false, "ldap_filter": false, "ldap_base_dn": "dc=test,dc=local", "ldap_bind_dn": "cn=admin,dc=test,dc=local", "ldap_bind_password": "testpass123"}', '{"ldap_ssl": "0", "ldap_host": "ldap.test.local", "ldap_port": "389", "ldap_ativo": "1", "ldap_filter": "(sAMAccountName={username})", "ldap_base_dn": "dc=test,dc=local", "ldap_bind_dn": "cn=admin,dc=test,dc=local", "ldap_bind_password": "testpass123"}', '::1', '', '2026-03-20 16:41:23.28052-03');
INSERT INTO public.tb_auditoria VALUES (1065, 'editar', 'configuracao', NULL, 'Grupo: scheduler', 1, 'admin', 'super_admin', '{"scheduler_ativo": false, "scheduler_retry": "1", "scheduler_timeout": "120", "scheduler_intervalo": "30", "scheduler_max_paralelo": "3", "scheduler_max_tentativas": false, "scheduler_intervalo_retry": false}', '{"scheduler_ativo": "1", "scheduler_retry": "1", "scheduler_timeout": "120", "scheduler_intervalo": "30", "scheduler_max_paralelo": "3", "scheduler_max_tentativas": "5", "scheduler_intervalo_retry": "600"}', '::1', '', '2026-03-20 16:41:23.666641-03');
INSERT INTO public.tb_auditoria VALUES (1066, 'editar', 'configuracao', NULL, 'Grupo: seguranca', 1, 'admin', 'super_admin', '{"2fa_ativo": false, "senha_min": false, "senha_numero": false, "sessao_tempo": false, "login_bloqueio": false, "senha_especial": false, "senha_maiuscula": false, "senha_minuscula": false, "login_tentativas": false}', '{"2fa_ativo": "0", "senha_min": "8", "senha_numero": "1", "sessao_tempo": "120", "login_bloqueio": "15", "senha_especial": "0", "senha_maiuscula": "1", "senha_minuscula": "1", "login_tentativas": "5"}', '::1', '', '2026-03-20 16:41:23.934517-03');
INSERT INTO public.tb_auditoria VALUES (1067, 'editar', 'configuracao', NULL, 'Grupo: notificacoes', 1, 'admin', 'super_admin', '{"notif_falha": false, "notif_emails": false, "notif_conexao": false, "notif_sistema": false, "notif_sucesso": false, "notif_agendamento": false}', '{"notif_falha": "1", "notif_emails": "test@test.com\nadmin@test.com", "notif_conexao": "1", "notif_sistema": "0", "notif_sucesso": "0", "notif_agendamento": "0"}', '::1', '', '2026-03-20 16:41:24.183306-03');
INSERT INTO public.tb_auditoria VALUES (1068, 'limpar', 'sistema', NULL, 'Limpeza: 0 registros removidos (> 365 dias)', 1, 'admin', 'super_admin', '[]', '[]', '::1', '', '2026-03-20 16:41:24.745832-03');
INSERT INTO public.tb_auditoria VALUES (1069, 'importar', 'configuracao', NULL, 'Importação: 1 campos', 1, 'admin', 'super_admin', '[]', '[]', '::1', '', '2026-03-20 16:41:24.89094-03');
INSERT INTO public.tb_auditoria VALUES (1070, 'editar', 'configuracao', NULL, 'Grupo: geral', 1, 'admin', 'super_admin', '{"app_nome": "DMC Importado"}', '{"app_nome": "DMC DataLoad"}', '::1', '', '2026-03-20 16:41:25.16467-03');
INSERT INTO public.tb_auditoria VALUES (1071, 'login', 'sessao', 32, 'lucas', 32, 'lucas', 'desenvolvedor', '[]', '[]', '::1', '', '2026-03-20 16:41:38.998205-03');
INSERT INTO public.tb_auditoria VALUES (1072, 'login', 'sessao', 1, 'admin', 1, 'admin', 'super_admin', '[]', '[]', '::1', '', '2026-03-20 16:41:41.508068-03');
INSERT INTO public.tb_auditoria VALUES (1073, 'login', 'sessao', 3, 'leo', 3, 'leo', 'desenvolvedor', '[]', '[]', '::1', '', '2026-03-20 16:41:44.361104-03');
INSERT INTO public.tb_auditoria VALUES (1074, 'login', 'sessao', 23, 'renan', 23, 'renan', 'operador', '[]', '[]', '::1', '', '2026-03-20 16:41:46.75869-03');
INSERT INTO public.tb_auditoria VALUES (1075, 'login', 'sessao', 107, 'maria_infra', 107, 'maria_infra', 'desenvolvedor', '[]', '[]', '::1', '', '2026-03-20 16:41:48.576806-03');
INSERT INTO public.tb_auditoria VALUES (1076, 'login', 'sessao', 108, 'joao_saude', 108, 'joao_saude', 'operador', '[]', '[]', '::1', '', '2026-03-20 16:41:51.223426-03');
INSERT INTO public.tb_auditoria VALUES (1077, 'login', 'sessao', 110, 'pedro_agencia', 110, 'pedro_agencia', 'desenvolvedor', '[]', '[]', '::1', '', '2026-03-20 16:41:53.82387-03');
INSERT INTO public.tb_auditoria VALUES (1078, 'login', 'sessao', 1, 'admin', 1, 'admin', 'super_admin', '[]', '[]', '::1', '', '2026-03-20 17:02:17.521918-03');
INSERT INTO public.tb_auditoria VALUES (1079, 'logout', 'sessao', NULL, NULL, 23, 'renan', 'operador', '[]', '[]', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-20 17:15:35.20275-03');
INSERT INTO public.tb_auditoria VALUES (1080, 'login', 'sessao', 23, 'renan', 23, 'renan', 'operador', '[]', '[]', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-20 17:19:24.271746-03');
INSERT INTO public.tb_auditoria VALUES (1081, 'logout', 'sessao', NULL, NULL, 23, 'renan', 'operador', '[]', '[]', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-20 17:19:27.500716-03');
INSERT INTO public.tb_auditoria VALUES (1082, 'editar', 'configuracao', NULL, 'Grupo: geral', 1, 'admin', 'super_admin', '{"app_url": "http://localhost/DMC-DATALOAD/public", "app_nome": "DMC DataLoad", "app_idioma": "pt_BR", "app_timezone": "America/Sao_Paulo", "app_descricao": "Descrição teste v2", "login_bg_imagem": false, "modo_manutencao": "0"}', '{"app_url": "http://localhost/DMC-DATALOAD/public", "app_nome": "DMC DataLoad", "app_idioma": "pt_BR", "app_timezone": "America/Sao_Paulo", "app_descricao": "Descrição teste v2", "login_bg_imagem": "https://img.freepik.com/premium-vector/realistic-big-data-illustration_23-2151555668.jpg", "modo_manutencao": "0"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-20 17:20:30.793184-03');
INSERT INTO public.tb_auditoria VALUES (1083, 'logout', 'sessao', NULL, NULL, 1, 'admin', 'super_admin', '[]', '[]', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-20 17:20:35.925608-03');
INSERT INTO public.tb_auditoria VALUES (1084, 'login', 'sessao', 1, 'admin', 1, 'admin', 'super_admin', '[]', '[]', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-20 17:21:44.137285-03');
INSERT INTO public.tb_auditoria VALUES (1085, 'editar', 'configuracao', NULL, 'Grupo: geral', 1, 'admin', 'super_admin', '{"app_url": "http://localhost/DMC-DATALOAD/public", "app_nome": "DMC DataLoad", "app_idioma": "pt_BR", "app_timezone": "America/Sao_Paulo", "app_descricao": "Descrição teste v2", "login_bg_imagem": "https://img.freepik.com/premium-vector/realistic-big-data-illustration_23-2151555668.jpg", "modo_manutencao": "0"}', '{"app_url": "http://localhost/DMC-DATALOAD/public", "app_nome": "DMC DataLoad", "app_idioma": "pt_BR", "app_timezone": "America/Sao_Paulo", "app_descricao": "Descrição teste v2", "login_bg_imagem": "https://t3.ftcdn.net/jpg/17/33/59/26/360_F_1733592614_n8uzQbb4VJjNWvJMDo9PEpzCfBharjEZ.jpg", "modo_manutencao": "0"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-20 17:21:51.604928-03');
INSERT INTO public.tb_auditoria VALUES (1086, 'editar', 'configuracao', NULL, 'Grupo: geral', 1, 'admin', 'super_admin', '{"app_url": "http://localhost/DMC-DATALOAD/public", "app_nome": "DMC DataLoad", "app_idioma": "pt_BR", "app_timezone": "America/Sao_Paulo", "app_descricao": "Descrição teste v2", "login_bg_imagem": "https://t3.ftcdn.net/jpg/17/33/59/26/360_F_1733592614_n8uzQbb4VJjNWvJMDo9PEpzCfBharjEZ.jpg", "modo_manutencao": "0"}', '{"app_url": "http://localhost/DMC-DATALOAD/public", "app_nome": "DMC DataLoad", "app_idioma": "pt_BR", "app_timezone": "America/Sao_Paulo", "app_descricao": "Descrição teste v2", "login_bg_imagem": "https://i.deeezy.com/uploads/thumbnails/compressed/315/31572-71ac68d32dcb74a5ae39d1c4a274138c.jpg", "modo_manutencao": "0"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-20 17:23:10.363694-03');
INSERT INTO public.tb_auditoria VALUES (1087, 'editar', 'configuracao', NULL, 'Grupo: geral', 1, 'admin', 'super_admin', '{"app_url": "http://localhost/DMC-DATALOAD/public", "app_nome": "DMC DataLoad", "app_idioma": "pt_BR", "app_timezone": "America/Sao_Paulo", "app_descricao": "Descrição teste v2", "login_bg_imagem": "https://i.deeezy.com/uploads/thumbnails/compressed/315/31572-71ac68d32dcb74a5ae39d1c4a274138c.jpg", "modo_manutencao": "0"}', '{"app_url": "http://localhost/DMC-DATALOAD/public", "app_nome": "DMC DataLoad", "app_idioma": "pt_BR", "app_timezone": "America/Sao_Paulo", "app_descricao": "Descrição teste v2", "login_bg_imagem": "https://img.freepik.com/vetores-premium/ilustracao-de-gradiente-sql_52683-80408.jpg", "modo_manutencao": "0"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-20 17:23:45.335867-03');
INSERT INTO public.tb_auditoria VALUES (1088, 'editar', 'configuracao', NULL, 'Grupo: geral', 1, 'admin', 'super_admin', '{"app_url": "http://localhost/DMC-DATALOAD/public", "app_nome": "DMC DataLoad", "app_idioma": "pt_BR", "app_timezone": "America/Sao_Paulo", "app_descricao": "Descrição teste v2", "login_bg_imagem": "https://img.freepik.com/vetores-premium/ilustracao-de-gradiente-sql_52683-80408.jpg", "modo_manutencao": "0"}', '{"app_url": "http://localhost/DMC-DATALOAD/public", "app_nome": "DMC DataLoad", "app_idioma": "pt_BR", "app_timezone": "America/Sao_Paulo", "app_descricao": "Descrição teste v2", "login_bg_imagem": "https://img.freepik.com/free-vector/creative-abstract-sql-illustration_52683-79681.jpg", "modo_manutencao": "0"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-20 17:25:43.646738-03');
INSERT INTO public.tb_auditoria VALUES (1089, 'editar', 'configuracao', NULL, 'Grupo: geral', 1, 'admin', 'super_admin', '{"app_url": "http://localhost/DMC-DATALOAD/public", "app_nome": "DMC DataLoad", "app_idioma": "pt_BR", "app_timezone": "America/Sao_Paulo", "app_descricao": "Descrição teste v2", "login_bg_imagem": "https://img.freepik.com/free-vector/creative-abstract-sql-illustration_52683-79681.jpg", "modo_manutencao": "0"}', '{"app_url": "http://localhost/DMC-DATALOAD/public", "app_nome": "DMC - DataLoad", "app_idioma": "pt_BR", "app_timezone": "America/Sao_Paulo", "app_descricao": "Descrição teste v2", "login_bg_imagem": "https://img.freepik.com/free-vector/creative-abstract-sql-illustration_52683-79681.jpg", "modo_manutencao": "0"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-20 17:26:21.533027-03');
INSERT INTO public.tb_auditoria VALUES (1090, 'editar', 'configuracao', NULL, 'Grupo: notificacoes', 1, 'admin', 'super_admin', '{"notif_falha": "1", "notif_emails": "test@test.com\nadmin@test.com", "notif_conexao": "1", "notif_sistema": "0", "notif_sucesso": "0", "notif_agendamento": "0"}', '{"notif_falha": "1", "notif_emails": "dmc@dynamicmotioncentury.com.br", "notif_conexao": "1", "notif_sistema": "0", "notif_sucesso": "0", "notif_agendamento": "0"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-20 17:38:18.734739-03');
INSERT INTO public.tb_auditoria VALUES (1091, 'editar', 'configuracao', NULL, 'Grupo: notificacoes', 1, 'admin', 'super_admin', '{"notif_falha": "1", "notif_emails": "dmc@dynamicmotioncentury.com.br", "notif_conexao": "1", "notif_sistema": "0", "notif_sucesso": "0", "notif_agendamento": "0"}', '{"notif_falha": "1", "notif_emails": "", "notif_conexao": "1", "notif_sistema": "0", "notif_sucesso": "0", "notif_agendamento": "0"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-20 17:38:31.394317-03');
INSERT INTO public.tb_auditoria VALUES (1092, 'editar', 'usuario', 13, 'caio', 1, 'admin', 'super_admin', '[]', '{"nivel_acesso": "admin"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-20 17:50:58.869406-03');
INSERT INTO public.tb_auditoria VALUES (1093, 'senha_redefinida', 'usuario', 13, 'caio', 1, 'admin', 'super_admin', '[]', '[]', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-20 17:52:27.96483-03');
INSERT INTO public.tb_auditoria VALUES (1094, 'login', 'sessao', 13, 'caio', 13, 'caio', 'admin', '[]', '[]', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-20 17:52:35.777932-03');


--
-- TOC entry 5433 (class 0 OID 45495)
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
-- TOC entry 5504 (class 0 OID 46588)
-- Dependencies: 292
-- Data for Name: tb_backups; Type: TABLE DATA; Schema: public; Owner: postgres
--

INSERT INTO public.tb_backups VALUES (58, 'backup_completo_2026-03-20_201849', 'completo', 80554, 'C:\xampp\htdocs\DMC-DATALOAD\app\Servicos/../../backups/backup_completo_2026-03-20_201849.json', 'aa80547c23d711c8e9ef4d42b488e2bd767c069c1ee48060a2935bebaf763d88', 'concluido', NULL, 1, 'admin', '2026-03-20 16:18:49.488094-03', '2026-03-20 16:18:49.505635-03');
INSERT INTO public.tb_backups VALUES (59, 'backup_completo_2026-03-20_204124', 'completo', 88574, 'C:\xampp\htdocs\DMC-DATALOAD\app\Servicos/../../backups/backup_completo_2026-03-20_204124.json', '61c803005d76c5a8ec82813926867d5adbe5ba4594cda4ada3191d10fe243611', 'concluido', NULL, 1, 'admin', '2026-03-20 16:41:24.571996-03', '2026-03-20 16:41:24.602447-03');


--
-- TOC entry 5445 (class 0 OID 45592)
-- Dependencies: 232
-- Data for Name: tb_blocos_rotina; Type: TABLE DATA; Schema: public; Owner: postgres
--

INSERT INTO public.tb_blocos_rotina OVERRIDING SYSTEM VALUE VALUES (62, 38, 'bloco_1', 1, 'SELECT * FROM audit_log ORDER BY id DESC LIMIT 15', 'sql', '2026-03-19 13:36:05.650196-03');
INSERT INTO public.tb_blocos_rotina OVERRIDING SYSTEM VALUE VALUES (41, 25, 'step_1', 1, 'SELECT id, codigo_bloco, ordem FROM tb_blocos_rotina LIMIT 5', 'SELECT', '2026-02-03 08:44:49.881989-03');
INSERT INTO public.tb_blocos_rotina OVERRIDING SYSTEM VALUE VALUES (44, 31, 'bloco_1', 1, 'SELECT id, titulo, status, data_criacao FROM chamados ORDER BY data_criacao DESC LIMIT 10', 'sql', '2026-03-19 13:20:01.512039-03');
INSERT INTO public.tb_blocos_rotina OVERRIDING SYSTEM VALUE VALUES (48, 35, 'bloco_1', 1, 'SELECT s.id, s.nome as setor FROM cfg_setor s ORDER BY s.nome', 'sql', '2026-03-19 13:20:17.222496-03');
INSERT INTO public.tb_blocos_rotina OVERRIDING SYSTEM VALUE VALUES (49, 36, 'bloco_1', 1, 'SELECT id, action, created_at FROM audit_log ORDER BY created_at DESC LIMIT 15', 'sql', '2026-03-19 13:20:17.39436-03');
INSERT INTO public.tb_blocos_rotina OVERRIDING SYSTEM VALUE VALUES (50, 37, 'bloco_1', 1, 'SELECT s.id, s.nome as setor FROM cfg_setor s ORDER BY s.nome', 'sql', '2026-03-19 13:20:19.881585-03');
INSERT INTO public.tb_blocos_rotina OVERRIDING SYSTEM VALUE VALUES (52, 39, 'bloco_1', 1, 'SELECT * FROM tb_cfg_apartamentos LIMIT 20', 'sql', '2026-03-19 13:20:26.416846-03');
INSERT INTO public.tb_blocos_rotina OVERRIDING SYSTEM VALUE VALUES (53, 40, 'bloco_1', 1, 'SELECT * FROM tb_avisos ORDER BY id DESC LIMIT 15', 'sql', '2026-03-19 13:20:33.051762-03');
INSERT INTO public.tb_blocos_rotina OVERRIDING SYSTEM VALUE VALUES (54, 41, 'bloco_1', 1, 'SELECT * FROM tb_cfg_areas_comuns LIMIT 20', 'sql', '2026-03-19 13:20:36.816857-03');
INSERT INTO public.tb_blocos_rotina OVERRIDING SYSTEM VALUE VALUES (55, 42, 'bloco_1', 1, 'SELECT * FROM tb_anuncios ORDER BY id DESC LIMIT 15', 'sql', '2026-03-19 13:20:37.036338-03');
INSERT INTO public.tb_blocos_rotina OVERRIDING SYSTEM VALUE VALUES (56, 43, 'bloco_1', 1, 'SELECT r.id, r.name as role_name, r.guard_name FROM roles r ORDER BY r.id', 'sql', '2026-03-19 13:20:37.245916-03');
INSERT INTO public.tb_blocos_rotina OVERRIDING SYSTEM VALUE VALUES (57, 44, 'bloco_1', 1, 'SELECT * FROM tb_cfg_areas_comuns LIMIT 20', 'sql', '2026-03-19 13:20:39.375938-03');
INSERT INTO public.tb_blocos_rotina OVERRIDING SYSTEM VALUE VALUES (58, 45, 'bloco_1', 1, 'SELECT * FROM tb_anuncios ORDER BY id DESC LIMIT 15', 'sql', '2026-03-19 13:20:43.258665-03');
INSERT INTO public.tb_blocos_rotina OVERRIDING SYSTEM VALUE VALUES (59, 46, 'bloco_1', 1, 'SELECT r.id, r.name as role_name, r.guard_name FROM roles r ORDER BY r.id', 'sql', '2026-03-19 13:20:45.820796-03');
INSERT INTO public.tb_blocos_rotina OVERRIDING SYSTEM VALUE VALUES (63, 32, 'bloco_1', 1, 'SELECT * FROM tb_chamado ORDER BY id DESC LIMIT 10', 'sql', '2026-03-19 13:36:30.628744-03');
INSERT INTO public.tb_blocos_rotina OVERRIDING SYSTEM VALUE VALUES (64, 33, 'bloco_1', 1, 'SELECT id_status, COUNT(*) as total FROM tb_chamado GROUP BY id_status ORDER BY total DESC', 'SELECT', '2026-03-20 14:18:53.308506-03');
INSERT INTO public.tb_blocos_rotina OVERRIDING SYSTEM VALUE VALUES (65, 34, 'bloco_1', 1, 'SELECT * FROM cfg_configuracoes LIMIT 20', 'SELECT', '2026-03-20 14:19:57.656024-03');


--
-- TOC entry 5502 (class 0 OID 46570)
-- Dependencies: 290
-- Data for Name: tb_canais_notificacao; Type: TABLE DATA; Schema: public; Owner: postgres
--



--
-- TOC entry 5493 (class 0 OID 46469)
-- Dependencies: 281
-- Data for Name: tb_compartilhamentos; Type: TABLE DATA; Schema: public; Owner: postgres
--



--
-- TOC entry 5496 (class 0 OID 46524)
-- Dependencies: 284
-- Data for Name: tb_configuracoes; Type: TABLE DATA; Schema: public; Owner: postgres
--

INSERT INTO public.tb_configuracoes VALUES ('ldap_bind_dn', 'cn=admin,dc=test,dc=local', 'ldap', NULL, '2026-03-20 16:41:23.277429-03', 1);
INSERT INTO public.tb_configuracoes VALUES ('ldap_bind_password', 'testpass123', 'ldap', NULL, '2026-03-20 16:41:23.278975-03', 1);
INSERT INTO public.tb_configuracoes VALUES ('scheduler_ativo', '1', 'scheduler', NULL, '2026-03-20 16:41:23.653414-03', 1);
INSERT INTO public.tb_configuracoes VALUES ('scheduler_intervalo', '30', 'scheduler', 'Intervalo de verificação em segundos', '2026-03-20 16:41:23.65663-03', 1);
INSERT INTO public.tb_configuracoes VALUES ('scheduler_max_paralelo', '3', 'scheduler', 'Máximo de execuções paralelas', '2026-03-20 16:41:23.659254-03', 1);
INSERT INTO public.tb_configuracoes VALUES ('scheduler_timeout', '120', 'scheduler', 'Timeout de execução em segundos', '2026-03-20 16:41:23.66169-03', 1);
INSERT INTO public.tb_configuracoes VALUES ('scheduler_retry', '1', 'scheduler', NULL, '2026-03-20 16:41:23.663101-03', 1);
INSERT INTO public.tb_configuracoes VALUES ('scheduler_max_tentativas', '5', 'scheduler', NULL, '2026-03-20 16:41:23.664157-03', 1);
INSERT INTO public.tb_configuracoes VALUES ('scheduler_intervalo_retry', '600', 'scheduler', NULL, '2026-03-20 16:41:23.665157-03', 1);
INSERT INTO public.tb_configuracoes VALUES ('sessao_tempo', '120', 'seguranca', NULL, '2026-03-20 16:41:23.921966-03', 1);
INSERT INTO public.tb_configuracoes VALUES ('login_tentativas', '5', 'seguranca', NULL, '2026-03-20 16:41:23.924046-03', 1);
INSERT INTO public.tb_configuracoes VALUES ('login_bloqueio', '15', 'seguranca', NULL, '2026-03-20 16:41:23.925137-03', 1);
INSERT INTO public.tb_configuracoes VALUES ('senha_min', '8', 'seguranca', NULL, '2026-03-20 16:41:23.926153-03', 1);
INSERT INTO public.tb_configuracoes VALUES ('senha_maiuscula', '1', 'seguranca', NULL, '2026-03-20 16:41:23.927142-03', 1);
INSERT INTO public.tb_configuracoes VALUES ('senha_minuscula', '1', 'seguranca', NULL, '2026-03-20 16:41:23.928095-03', 1);
INSERT INTO public.tb_configuracoes VALUES ('smtp_host', 'smtp.hostinger.com', 'email', 'Servidor SMTP', '2026-03-20 16:27:36.481176-03', 1);
INSERT INTO public.tb_configuracoes VALUES ('smtp_port', '587', 'email', 'Porta SMTP', '2026-03-20 16:27:36.482116-03', 1);
INSERT INTO public.tb_configuracoes VALUES ('smtp_encryption', 'tls', 'email', 'Criptografia SMTP', '2026-03-20 16:27:36.483345-03', 1);
INSERT INTO public.tb_configuracoes VALUES ('smtp_user', 'dmc@dynamicmotioncentury.com.br', 'email', 'Usuário SMTP', '2026-03-20 16:27:36.484416-03', 1);
INSERT INTO public.tb_configuracoes VALUES ('smtp_password', 'Caiodmc2022@', 'email', 'Senha SMTP', '2026-03-20 16:27:36.485504-03', 1);
INSERT INTO public.tb_configuracoes VALUES ('smtp_from_email', 'dmc@dynamicmotioncentury.com.br', 'email', 'E-mail remetente', '2026-03-20 16:27:36.486659-03', 1);
INSERT INTO public.tb_configuracoes VALUES ('smtp_from_name', 'DMC DataLoad', 'email', 'Nome remetente', '2026-03-20 16:27:36.488026-03', 1);
INSERT INTO public.tb_configuracoes VALUES ('senha_numero', '1', 'seguranca', NULL, '2026-03-20 16:41:23.929325-03', 1);
INSERT INTO public.tb_configuracoes VALUES ('senha_especial', '0', 'seguranca', NULL, '2026-03-20 16:41:23.931134-03', 1);
INSERT INTO public.tb_configuracoes VALUES ('2fa_ativo', '0', 'seguranca', NULL, '2026-03-20 16:41:23.932784-03', 1);
INSERT INTO public.tb_configuracoes VALUES ('app_manutencao', '0', 'geral', 'Modo manutenção', '2026-03-20 16:35:23.965454-03', 1);
INSERT INTO public.tb_configuracoes VALUES ('ldap_filtro', '(sAMAccountName={username})', 'ldap', NULL, '2026-03-20 16:35:24.091994-03', 1);
INSERT INTO public.tb_configuracoes VALUES ('seguranca_timeout_sessao', '120', 'seguranca', 'Timeout de sessão em segundos', '2026-03-20 16:35:24.623038-03', 1);
INSERT INTO public.tb_configuracoes VALUES ('seguranca_tentativas_login', '5', 'seguranca', 'Tentativas de login antes de bloqueio', '2026-03-20 16:35:24.625081-03', 1);
INSERT INTO public.tb_configuracoes VALUES ('seguranca_tempo_bloqueio', '15', 'seguranca', 'Tempo de bloqueio em segundos', '2026-03-20 16:35:24.626939-03', 1);
INSERT INTO public.tb_configuracoes VALUES ('notif_email_falha', '1', 'notificacoes', 'Enviar e-mail em falha de execução', '2026-03-20 16:35:24.875573-03', 1);
INSERT INTO public.tb_configuracoes VALUES ('notif_webhook_ativo', '0', 'notificacoes', 'Ativar webhooks de notificação', '2026-03-20 16:35:24.876996-03', 1);
INSERT INTO public.tb_configuracoes VALUES ('notif_webhook_url', '', 'notificacoes', 'URL do webhook padrão', '2026-03-20 16:35:24.878393-03', 1);
INSERT INTO public.tb_configuracoes VALUES ('notif_email_sucesso', '0', 'notificacoes', NULL, '2026-03-20 16:35:24.880192-03', 1);
INSERT INTO public.tb_configuracoes VALUES ('notif_falha', '1', 'notificacoes', NULL, '2026-03-20 17:38:31.384358-03', 1);
INSERT INTO public.tb_configuracoes VALUES ('ldap_ativo', '1', 'ldap', NULL, '2026-03-20 16:41:23.267497-03', 1);
INSERT INTO public.tb_configuracoes VALUES ('ldap_host', 'ldap.test.local', 'ldap', NULL, '2026-03-20 16:41:23.269468-03', 1);
INSERT INTO public.tb_configuracoes VALUES ('ldap_port', '389', 'ldap', NULL, '2026-03-20 16:41:23.270908-03', 1);
INSERT INTO public.tb_configuracoes VALUES ('ldap_ssl', '0', 'ldap', NULL, '2026-03-20 16:41:23.272521-03', 1);
INSERT INTO public.tb_configuracoes VALUES ('ldap_base_dn', 'dc=test,dc=local', 'ldap', NULL, '2026-03-20 16:41:23.274234-03', 1);
INSERT INTO public.tb_configuracoes VALUES ('ldap_filter', '(sAMAccountName={username})', 'ldap', NULL, '2026-03-20 16:41:23.275659-03', 1);
INSERT INTO public.tb_configuracoes VALUES ('notif_conexao', '1', 'notificacoes', NULL, '2026-03-20 17:38:31.386588-03', 1);
INSERT INTO public.tb_configuracoes VALUES ('notif_emails', '', 'notificacoes', NULL, '2026-03-20 17:38:31.388024-03', 1);
INSERT INTO public.tb_configuracoes VALUES ('notif_sucesso', '0', 'notificacoes', NULL, '2026-03-20 17:38:31.389378-03', 1);
INSERT INTO public.tb_configuracoes VALUES ('notif_agendamento', '0', 'notificacoes', NULL, '2026-03-20 17:38:31.390991-03', 1);
INSERT INTO public.tb_configuracoes VALUES ('notif_sistema', '0', 'notificacoes', NULL, '2026-03-20 17:38:31.392639-03', 1);
INSERT INTO public.tb_configuracoes VALUES ('ip_tentativas', '10', 'seguranca', NULL, '2026-03-20 17:25:48.329404-03', NULL);
INSERT INTO public.tb_configuracoes VALUES ('ip_bloqueio', '15', 'seguranca', NULL, '2026-03-20 17:25:48.330697-03', NULL);
INSERT INTO public.tb_configuracoes VALUES ('app_nome', 'DMC - DataLoad', 'geral', 'Nome da aplicação', '2026-03-20 17:26:21.518783-03', 1);
INSERT INTO public.tb_configuracoes VALUES ('app_url', 'http://localhost/DMC-DATALOAD/public', 'geral', NULL, '2026-03-20 17:26:21.519739-03', 1);
INSERT INTO public.tb_configuracoes VALUES ('app_timezone', 'America/Sao_Paulo', 'geral', 'Timezone padrão', '2026-03-20 17:26:21.520933-03', 1);
INSERT INTO public.tb_configuracoes VALUES ('app_idioma', 'pt_BR', 'geral', 'Idioma padrão', '2026-03-20 17:26:21.527124-03', 1);
INSERT INTO public.tb_configuracoes VALUES ('app_descricao', 'Descrição teste v2', 'geral', NULL, '2026-03-20 17:26:21.528775-03', 1);
INSERT INTO public.tb_configuracoes VALUES ('login_bg_imagem', 'https://img.freepik.com/free-vector/creative-abstract-sql-illustration_52683-79681.jpg', 'geral', NULL, '2026-03-20 17:26:21.530423-03', 1);
INSERT INTO public.tb_configuracoes VALUES ('modo_manutencao', '0', 'geral', NULL, '2026-03-20 17:26:21.531569-03', 1);


--
-- TOC entry 5481 (class 0 OID 46345)
-- Dependencies: 269
-- Data for Name: tb_empresas; Type: TABLE DATA; Schema: public; Owner: postgres
--

INSERT INTO public.tb_empresas VALUES (1, 'Secretariá de administração', '', true, 1, '2026-03-19 19:25:43.513597-03', '2026-03-19 19:25:43.513597-03');
INSERT INTO public.tb_empresas VALUES (65, 'Departamento de Infraestrutura', NULL, true, NULL, '2026-03-20 14:06:40.05574-03', '2026-03-20 14:06:40.05574-03');
INSERT INTO public.tb_empresas VALUES (66, 'Secretaria de Saúde', NULL, true, NULL, '2026-03-20 14:06:40.063495-03', '2026-03-20 14:06:40.063495-03');
INSERT INTO public.tb_empresas VALUES (40, 'Agência de tecnologia e inovação', '', true, 1, '2026-03-20 11:41:13.613321-03', '2026-03-20 11:41:13.613321-03');


--
-- TOC entry 5459 (class 0 OID 45732)
-- Dependencies: 247
-- Data for Name: tb_eventos_api; Type: TABLE DATA; Schema: public; Owner: postgres
--

INSERT INTO public.tb_eventos_api VALUES (1, 3, 'Post ID Maior que 50', 'Dispara quando encontra post com ID > 50', '$[0].id', NULL, 'number', 'greater_than', '50', 'trigger_workflow', 1, true, true, NULL, NULL, NULL, 0, '2026-02-04 17:00:25.594742-03', '2026-02-04 17:00:25.594742-03', 1);
INSERT INTO public.tb_eventos_api VALUES (10, 19, 'Evento_Users_Count', NULL, '$.length', NULL, 'number', 'greater_than', '5', 'store_value', NULL, true, true, NULL, NULL, NULL, 0, '2026-03-19 13:34:23.761322-03', '2026-03-19 13:34:23.761322-03', 1);
INSERT INTO public.tb_eventos_api VALUES (11, 23, 'Evento_Comments_Email', NULL, '$.[0].email', NULL, 'string', 'contains', '@', 'store_value', NULL, true, true, NULL, NULL, NULL, 0, '2026-03-19 13:34:23.934911-03', '2026-03-19 13:34:23.934911-03', 1);
INSERT INTO public.tb_eventos_api VALUES (12, 24, 'Evento_Todos_Completed', NULL, '$.[0].completed', NULL, 'string', 'equals', 'false', 'store_value', NULL, true, true, NULL, NULL, NULL, 0, '2026-03-19 13:34:24.101594-03', '2026-03-19 13:34:24.101594-03', 1);
INSERT INTO public.tb_eventos_api VALUES (13, 25, 'Evento_Albums_Title', NULL, '$.[0].title', NULL, 'string', 'not_empty', '', 'store_value', NULL, true, true, NULL, NULL, NULL, 0, '2026-03-19 13:34:24.273595-03', '2026-03-19 13:34:24.273595-03', 1);
INSERT INTO public.tb_eventos_api VALUES (9, 18, 'Evento_Posts_UserID_1', NULL, '$.[0].userId', NULL, 'number', 'equals', '1', 'store_value', NULL, true, true, NULL, '1', true, 1, '2026-03-19 13:34:16.770784-03', '2026-03-19 13:39:08.163562-03', 1);


--
-- TOC entry 5500 (class 0 OID 46550)
-- Dependencies: 288
-- Data for Name: tb_fila_execucao; Type: TABLE DATA; Schema: public; Owner: postgres
--

INSERT INTO public.tb_fila_execucao VALUES (5, 'rotina', 999, 'Rotina Teste Fila', 'cancelado', 3, 0, 3, '2026-03-20 00:11:31-03', NULL, '2026-03-19 20:11:31.665514-03', NULL, NULL, 1, 'admin', NULL, '2026-03-19 20:11:31.204831-03');
INSERT INTO public.tb_fila_execucao VALUES (6, 'rotina', 999, 'Rotina Teste Fila', 'cancelado', 3, 0, 3, '2026-03-20 00:32:04-03', NULL, '2026-03-19 20:32:04.501553-03', NULL, NULL, 1, 'admin', NULL, '2026-03-19 20:32:04.124354-03');
INSERT INTO public.tb_fila_execucao VALUES (7, 'rotina', 999, 'Rotina Teste Fila', 'cancelado', 3, 0, 3, '2026-03-20 00:52:32-03', NULL, '2026-03-19 20:52:32.835178-03', NULL, NULL, 1, 'admin', NULL, '2026-03-19 20:52:32.522885-03');
INSERT INTO public.tb_fila_execucao VALUES (8, 'rotina', 999, 'Rotina Teste Fila', 'cancelado', 3, 0, 3, '2026-03-20 14:31:20-03', NULL, '2026-03-20 10:31:20.588023-03', NULL, NULL, 1, 'admin', NULL, '2026-03-20 10:31:20.255563-03');
INSERT INTO public.tb_fila_execucao VALUES (9, 'rotina', 999, 'Rotina Teste Fila', 'cancelado', 3, 0, 3, '2026-03-20 14:54:36-03', NULL, '2026-03-20 10:54:36.501316-03', NULL, NULL, 1, 'admin', NULL, '2026-03-20 10:54:36.252631-03');
INSERT INTO public.tb_fila_execucao VALUES (10, 'rotina', 999, 'Rotina Teste Fila', 'cancelado', 3, 0, 3, '2026-03-20 15:09:31-03', NULL, '2026-03-20 11:09:31.467563-03', NULL, NULL, 1, 'admin', NULL, '2026-03-20 11:09:31.093447-03');
INSERT INTO public.tb_fila_execucao VALUES (11, 'rotina', 999, 'Rotina Teste Fila', 'cancelado', 3, 0, 3, '2026-03-20 15:12:59-03', NULL, '2026-03-20 11:12:59.543057-03', NULL, NULL, 1, 'admin', NULL, '2026-03-20 11:12:59.201145-03');
INSERT INTO public.tb_fila_execucao VALUES (12, 'rotina', 999, 'Rotina Teste Fila', 'cancelado', 3, 0, 3, '2026-03-20 15:38:40-03', NULL, '2026-03-20 11:38:40.724485-03', NULL, NULL, 1, 'admin', NULL, '2026-03-20 11:38:40.328346-03');
INSERT INTO public.tb_fila_execucao VALUES (13, 'rotina', 999, 'Rotina Teste Fila', 'cancelado', 3, 0, 3, '2026-03-20 15:54:24-03', NULL, '2026-03-20 11:54:24.728383-03', NULL, NULL, 1, 'admin', NULL, '2026-03-20 11:54:24.318204-03');
INSERT INTO public.tb_fila_execucao VALUES (14, 'rotina', 999, 'Rotina Teste Fila', 'cancelado', 3, 0, 3, '2026-03-20 17:17:23-03', NULL, '2026-03-20 13:17:23.930628-03', NULL, NULL, 1, 'admin', NULL, '2026-03-20 13:17:23.598511-03');


--
-- TOC entry 5447 (class 0 OID 45607)
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
INSERT INTO public.tb_logs_execucao OVERRIDING SYSTEM VALUE VALUES (37, 34, '2026-03-19 13:35:28.292843-03', '2026-03-19 13:35:28.292843-03', 'sucesso', NULL, '[{"sql": "SELECT * FROM cfg_configuracoes LIMIT 20", "erro": null, "tipo": "sql", "bloco": "bloco_1", "ordem": 1, "status": "sucesso", "id_bloco": 47, "registros": 20, "resultado": "Linhas afetadas: 20", "duracao_ms": 6, "arquivo_csv": null}]', NULL, 152, 1, 1, 0, NULL, NULL, 20);
INSERT INTO public.tb_logs_execucao OVERRIDING SYSTEM VALUE VALUES (38, 37, '2026-03-19 13:35:29.005263-03', '2026-03-19 13:35:29.005263-03', 'sucesso', NULL, '[{"sql": "SELECT s.id, s.nome as setor FROM cfg_setor s ORDER BY s.nome", "erro": null, "tipo": "sql", "bloco": "bloco_1", "ordem": 1, "status": "sucesso", "id_bloco": 50, "registros": 5, "resultado": "Linhas afetadas: 5", "duracao_ms": 6, "arquivo_csv": null}]', NULL, 109, 1, 1, 0, NULL, NULL, 5);
INSERT INTO public.tb_logs_execucao OVERRIDING SYSTEM VALUE VALUES (39, 38, '2026-03-19 13:35:29.270335-03', '2026-03-19 13:35:29.270335-03', 'sucesso', NULL, '[{"sql": "SELECT id, action, created_at FROM audit_log ORDER BY created_at DESC LIMIT 15", "erro": "SQLSTATE[42703]: Undefined column: 7 ERRO:  coluna \"action\" não existe\nLINE 1: SELECT id, action, created_at FROM audit_log ORDER BY create...\n                   ^\nHINT:  Talvez você queira fazer referência à coluna \"audit_log.acao\".", "tipo": "sql", "bloco": "bloco_1", "ordem": 1, "status": "falha", "id_bloco": 51, "registros": 0, "resultado": "", "duracao_ms": 1, "arquivo_csv": null}]', NULL, 88, 1, 0, 1, NULL, NULL, 0);
INSERT INTO public.tb_logs_execucao OVERRIDING SYSTEM VALUE VALUES (40, 32, '2026-03-19 13:35:30.917774-03', '2026-03-19 13:35:30.917774-03', 'sucesso', NULL, '[{"sql": "SELECT id, titulo, status, data_criacao FROM chamados ORDER BY data_criacao DESC LIMIT 10", "erro": "SQLSTATE[42P01]: Undefined table: 7 ERRO:  relação \"chamados\" não existe\nLINE 1: SELECT id, titulo, status, data_criacao FROM chamados ORDER ...\n                                                     ^", "tipo": "sql", "bloco": "bloco_1", "ordem": 1, "status": "falha", "id_bloco": 45, "registros": 0, "resultado": "", "duracao_ms": 0, "arquivo_csv": null}]', NULL, 79, 1, 0, 1, NULL, NULL, 0);
INSERT INTO public.tb_logs_execucao OVERRIDING SYSTEM VALUE VALUES (41, 33, '2026-03-19 13:35:31.166644-03', '2026-03-19 13:35:31.166644-03', 'sucesso', NULL, '[{"sql": "SELECT status, COUNT(*) as total FROM chamados GROUP BY status ORDER BY total DESC", "erro": "SQLSTATE[42P01]: Undefined table: 7 ERRO:  relação \"chamados\" não existe\nLINE 1: SELECT status, COUNT(*) as total FROM chamados GROUP BY stat...\n                                              ^", "tipo": "sql", "bloco": "bloco_1", "ordem": 1, "status": "falha", "id_bloco": 46, "registros": 0, "resultado": "", "duracao_ms": 0, "arquivo_csv": null}]', NULL, 83, 1, 0, 1, NULL, NULL, 0);
INSERT INTO public.tb_logs_execucao OVERRIDING SYSTEM VALUE VALUES (42, 34, '2026-03-19 13:35:31.436758-03', '2026-03-19 13:35:31.436758-03', 'sucesso', NULL, '[{"sql": "SELECT * FROM cfg_configuracoes LIMIT 20", "erro": null, "tipo": "sql", "bloco": "bloco_1", "ordem": 1, "status": "sucesso", "id_bloco": 47, "registros": 20, "resultado": "Linhas afetadas: 20", "duracao_ms": 3, "arquivo_csv": null}]', NULL, 84, 1, 1, 0, NULL, NULL, 20);
INSERT INTO public.tb_logs_execucao OVERRIDING SYSTEM VALUE VALUES (43, 37, '2026-03-19 13:35:31.694553-03', '2026-03-19 13:35:31.694553-03', 'sucesso', NULL, '[{"sql": "SELECT s.id, s.nome as setor FROM cfg_setor s ORDER BY s.nome", "erro": null, "tipo": "sql", "bloco": "bloco_1", "ordem": 1, "status": "sucesso", "id_bloco": 50, "registros": 5, "resultado": "Linhas afetadas: 5", "duracao_ms": 4, "arquivo_csv": null}]', NULL, 91, 1, 1, 0, NULL, NULL, 5);
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
INSERT INTO public.tb_logs_execucao OVERRIDING SYSTEM VALUE VALUES (30, 25, '2026-02-03 17:04:28.785066-03', '2026-02-03 17:04:28.884568-03', 'sucesso', NULL, '[{"sql": "select * from public.users;", "erro": "SQLSTATE[42P01]: Undefined table: 7 ERRO:  relação \"public.users\" não existe\nLINE 1: select * from public.users;\n                      ^", "tipo": "SELECT", "bloco": "step_1", "ordem": 1, "status": "erro", "registros": 0, "resultado": null, "duracao_ms": 32}]', NULL, 82, 1, 0, 1, NULL, NULL, 0);
INSERT INTO public.tb_logs_execucao OVERRIDING SYSTEM VALUE VALUES (31, 25, '2026-02-03 17:05:08.972679-03', '2026-02-03 17:05:08.989835-03', 'sucesso', NULL, '[{"sql": "select * from public.users;", "erro": "SQLSTATE[42P01]: Undefined table: 7 ERRO:  relação \"public.users\" não existe\nLINE 1: select * from public.users;\n                      ^", "tipo": "SELECT", "bloco": "step_1", "ordem": 1, "status": "erro", "registros": 0, "resultado": null, "duracao_ms": 5}]', NULL, 13, 1, 0, 1, NULL, NULL, 0);
INSERT INTO public.tb_logs_execucao OVERRIDING SYSTEM VALUE VALUES (32, 25, '2026-02-03 17:05:33.65145-03', '2026-02-03 17:05:33.695163-03', 'sucesso', NULL, '[{"sql": "select * from public.users;", "erro": "SQLSTATE[42P01]: Undefined table: 7 ERRO:  relação \"public.users\" não existe\nLINE 1: select * from public.users;\n                      ^", "tipo": "SELECT", "bloco": "step_1", "ordem": 1, "status": "erro", "registros": 0, "resultado": null, "duracao_ms": 6}]', NULL, 30, 1, 0, 1, NULL, NULL, 0);
INSERT INTO public.tb_logs_execucao OVERRIDING SYSTEM VALUE VALUES (33, 25, '2026-02-03 17:07:21.96034-03', '2026-02-03 17:07:21.987304-03', 'sucesso', NULL, '[{"sql": "SELECT id, codigo_bloco, ordem FROM tb_blocos_rotina LIMIT 5", "erro": null, "tipo": "SELECT", "bloco": "step_1", "ordem": 1, "status": "sucesso", "registros": 1, "resultado": "Linhas afetadas: 1", "duracao_ms": 4}]', NULL, 20, 1, 1, 0, NULL, NULL, 1);
INSERT INTO public.tb_logs_execucao OVERRIDING SYSTEM VALUE VALUES (34, NULL, '2026-02-03 17:10:18.189387-03', '2026-02-03 17:10:18.189387-03', 'sucesso', NULL, '[{"sql": "select * from public.users;", "erro": null, "tipo": "SELECT", "bloco": "step_1", "ordem": 1, "status": "sucesso", "id_bloco": 42, "registros": 3, "resultado": "Arquivo gerado: C:\\xampp\\htdocs\\DMC-DATALOAD\\app\\Servicos/../../storage/logs\\execucao_30_step_1_1770149418.csv (Linhas: 3)", "duracao_ms": 10, "arquivo_csv": "C:\\xampp\\htdocs\\DMC-DATALOAD\\app\\Servicos/../../storage/logs\\execucao_30_step_1_1770149418.csv"}, {"sql": "", "erro": "PDO::query(): Argument #1 ($query) cannot be empty", "tipo": "SELECT", "bloco": "", "ordem": 2, "status": "falha", "id_bloco": 43, "registros": 0, "resultado": "", "duracao_ms": 0, "arquivo_csv": null}]', NULL, 72, 2, 1, 1, NULL, NULL, 3);
INSERT INTO public.tb_logs_execucao OVERRIDING SYSTEM VALUE VALUES (29, 25, '2026-02-03 17:00:24.657616-03', '2026-03-18 16:53:13.843149-03', 'erro', 'Execução interrompida - worker encerrado inesperadamente', NULL, NULL, NULL, 0, 0, 0, NULL, NULL, 0);
INSERT INTO public.tb_logs_execucao OVERRIDING SYSTEM VALUE VALUES (35, 32, '2026-03-19 13:35:27.628066-03', '2026-03-19 13:35:27.628066-03', 'sucesso', NULL, '[{"sql": "SELECT id, titulo, status, data_criacao FROM chamados ORDER BY data_criacao DESC LIMIT 10", "erro": "SQLSTATE[42P01]: Undefined table: 7 ERRO:  relação \"chamados\" não existe\nLINE 1: SELECT id, titulo, status, data_criacao FROM chamados ORDER ...\n                                                     ^", "tipo": "sql", "bloco": "bloco_1", "ordem": 1, "status": "falha", "id_bloco": 45, "registros": 0, "resultado": "", "duracao_ms": 5, "arquivo_csv": null}]', NULL, 129, 1, 0, 1, NULL, NULL, 0);
INSERT INTO public.tb_logs_execucao OVERRIDING SYSTEM VALUE VALUES (36, 33, '2026-03-19 13:35:27.903906-03', '2026-03-19 13:35:27.903906-03', 'sucesso', NULL, '[{"sql": "SELECT status, COUNT(*) as total FROM chamados GROUP BY status ORDER BY total DESC", "erro": "SQLSTATE[42P01]: Undefined table: 7 ERRO:  relação \"chamados\" não existe\nLINE 1: SELECT status, COUNT(*) as total FROM chamados GROUP BY stat...\n                                              ^", "tipo": "sql", "bloco": "bloco_1", "ordem": 1, "status": "falha", "id_bloco": 46, "registros": 0, "resultado": "", "duracao_ms": 1, "arquivo_csv": null}]', NULL, 96, 1, 0, 1, NULL, NULL, 0);
INSERT INTO public.tb_logs_execucao OVERRIDING SYSTEM VALUE VALUES (44, 38, '2026-03-19 13:35:31.970745-03', '2026-03-19 13:35:31.970745-03', 'sucesso', NULL, '[{"sql": "SELECT id, action, created_at FROM audit_log ORDER BY created_at DESC LIMIT 15", "erro": "SQLSTATE[42703]: Undefined column: 7 ERRO:  coluna \"action\" não existe\nLINE 1: SELECT id, action, created_at FROM audit_log ORDER BY create...\n                   ^\nHINT:  Talvez você queira fazer referência à coluna \"audit_log.acao\".", "tipo": "sql", "bloco": "bloco_1", "ordem": 1, "status": "falha", "id_bloco": 51, "registros": 0, "resultado": "", "duracao_ms": 1, "arquivo_csv": null}]', NULL, 94, 1, 0, 1, NULL, NULL, 0);
INSERT INTO public.tb_logs_execucao OVERRIDING SYSTEM VALUE VALUES (45, 34, '2026-03-19 13:35:48.80088-03', '2026-03-19 13:35:48.80088-03', 'sucesso', NULL, '[{"sql": "SELECT * FROM cfg_configuracoes LIMIT 20", "erro": null, "tipo": "sql", "bloco": "bloco_1", "ordem": 1, "status": "sucesso", "id_bloco": 47, "registros": 20, "resultado": "Linhas afetadas: 20", "duracao_ms": 3, "arquivo_csv": null}]', NULL, 115, 1, 1, 0, NULL, NULL, 20);
INSERT INTO public.tb_logs_execucao OVERRIDING SYSTEM VALUE VALUES (46, 32, '2026-03-19 13:36:12.435446-03', '2026-03-19 13:36:12.435446-03', 'sucesso', NULL, '[{"sql": "SELECT id, titulo, id_status, data_abertura FROM tb_chamado ORDER BY data_abertura DESC LIMIT 10", "erro": "SQLSTATE[42703]: Undefined column: 7 ERRO:  coluna \"data_abertura\" não existe\nLINE 1: SELECT id, titulo, id_status, data_abertura FROM tb_chamado ...\n                                      ^", "tipo": "sql", "bloco": "bloco_1", "ordem": 1, "status": "falha", "id_bloco": 60, "registros": 0, "resultado": "", "duracao_ms": 1, "arquivo_csv": null}]', NULL, 88, 1, 0, 1, NULL, NULL, 0);
INSERT INTO public.tb_logs_execucao OVERRIDING SYSTEM VALUE VALUES (47, 33, '2026-03-19 13:36:12.713454-03', '2026-03-19 13:36:12.713454-03', 'sucesso', NULL, '[{"sql": "SELECT id_status, COUNT(*) as total FROM tb_chamado GROUP BY id_status ORDER BY total DESC", "erro": null, "tipo": "sql", "bloco": "bloco_1", "ordem": 1, "status": "sucesso", "id_bloco": 61, "registros": 2, "resultado": "Linhas afetadas: 2", "duracao_ms": 8, "arquivo_csv": null}]', NULL, 102, 1, 1, 0, NULL, NULL, 2);
INSERT INTO public.tb_logs_execucao OVERRIDING SYSTEM VALUE VALUES (48, 34, '2026-03-19 13:36:12.992088-03', '2026-03-19 13:36:12.992088-03', 'sucesso', NULL, '[{"sql": "SELECT * FROM cfg_configuracoes LIMIT 20", "erro": null, "tipo": "sql", "bloco": "bloco_1", "ordem": 1, "status": "sucesso", "id_bloco": 47, "registros": 20, "resultado": "Linhas afetadas: 20", "duracao_ms": 3, "arquivo_csv": null}]', NULL, 94, 1, 1, 0, NULL, NULL, 20);
INSERT INTO public.tb_logs_execucao OVERRIDING SYSTEM VALUE VALUES (49, 37, '2026-03-19 13:36:13.291559-03', '2026-03-19 13:36:13.291559-03', 'sucesso', NULL, '[{"sql": "SELECT s.id, s.nome as setor FROM cfg_setor s ORDER BY s.nome", "erro": null, "tipo": "sql", "bloco": "bloco_1", "ordem": 1, "status": "sucesso", "id_bloco": 50, "registros": 5, "resultado": "Linhas afetadas: 5", "duracao_ms": 3, "arquivo_csv": null}]', NULL, 98, 1, 1, 0, NULL, NULL, 5);
INSERT INTO public.tb_logs_execucao OVERRIDING SYSTEM VALUE VALUES (50, 38, '2026-03-19 13:36:13.555612-03', '2026-03-19 13:36:13.555612-03', 'sucesso', NULL, '[{"sql": "SELECT * FROM audit_log ORDER BY id DESC LIMIT 15", "erro": null, "tipo": "sql", "bloco": "bloco_1", "ordem": 1, "status": "sucesso", "id_bloco": 62, "registros": 15, "resultado": "Linhas afetadas: 15", "duracao_ms": 6, "arquivo_csv": null}]', NULL, 97, 1, 1, 0, NULL, NULL, 15);
INSERT INTO public.tb_logs_execucao OVERRIDING SYSTEM VALUE VALUES (51, 39, '2026-03-19 13:36:13.758891-03', '2026-03-19 13:36:13.758891-03', 'falha', 'Não foi possível conectar ao banco alvo', '[]', NULL, 21, 0, 0, 0, NULL, NULL, 0);
INSERT INTO public.tb_logs_execucao OVERRIDING SYSTEM VALUE VALUES (52, 40, '2026-03-19 13:36:14.348015-03', '2026-03-19 13:36:14.348015-03', 'falha', 'Não foi possível conectar ao banco alvo', '[]', NULL, 32, 0, 0, 0, NULL, NULL, 0);
INSERT INTO public.tb_logs_execucao OVERRIDING SYSTEM VALUE VALUES (53, 44, '2026-03-19 13:36:14.53759-03', '2026-03-19 13:36:14.53759-03', 'falha', 'Não foi possível conectar ao banco alvo', '[]', NULL, 22, 0, 0, 0, NULL, NULL, 0);
INSERT INTO public.tb_logs_execucao OVERRIDING SYSTEM VALUE VALUES (54, 45, '2026-03-19 13:36:14.730316-03', '2026-03-19 13:36:14.730316-03', 'falha', 'Não foi possível conectar ao banco alvo', '[]', NULL, 27, 0, 0, 0, NULL, NULL, 0);
INSERT INTO public.tb_logs_execucao OVERRIDING SYSTEM VALUE VALUES (55, 46, '2026-03-19 13:36:14.917827-03', '2026-03-19 13:36:14.917827-03', 'falha', 'Não foi possível conectar ao banco alvo', '[]', NULL, 21, 0, 0, 0, NULL, NULL, 0);
INSERT INTO public.tb_logs_execucao OVERRIDING SYSTEM VALUE VALUES (56, 32, '2026-03-19 13:36:16.356363-03', '2026-03-19 13:36:16.356363-03', 'sucesso', NULL, '[{"sql": "SELECT id, titulo, id_status, data_abertura FROM tb_chamado ORDER BY data_abertura DESC LIMIT 10", "erro": "SQLSTATE[42703]: Undefined column: 7 ERRO:  coluna \"data_abertura\" não existe\nLINE 1: SELECT id, titulo, id_status, data_abertura FROM tb_chamado ...\n                                      ^", "tipo": "sql", "bloco": "bloco_1", "ordem": 1, "status": "falha", "id_bloco": 60, "registros": 0, "resultado": "", "duracao_ms": 1, "arquivo_csv": null}]', NULL, 80, 1, 0, 1, NULL, NULL, 0);
INSERT INTO public.tb_logs_execucao OVERRIDING SYSTEM VALUE VALUES (57, 33, '2026-03-19 13:36:16.593949-03', '2026-03-19 13:36:16.593949-03', 'sucesso', NULL, '[{"sql": "SELECT id_status, COUNT(*) as total FROM tb_chamado GROUP BY id_status ORDER BY total DESC", "erro": null, "tipo": "sql", "bloco": "bloco_1", "ordem": 1, "status": "sucesso", "id_bloco": 61, "registros": 2, "resultado": "Linhas afetadas: 2", "duracao_ms": 4, "arquivo_csv": null}]', NULL, 83, 1, 1, 0, NULL, NULL, 2);
INSERT INTO public.tb_logs_execucao OVERRIDING SYSTEM VALUE VALUES (58, 34, '2026-03-19 13:36:16.837824-03', '2026-03-19 13:36:16.837824-03', 'sucesso', NULL, '[{"sql": "SELECT * FROM cfg_configuracoes LIMIT 20", "erro": null, "tipo": "sql", "bloco": "bloco_1", "ordem": 1, "status": "sucesso", "id_bloco": 47, "registros": 20, "resultado": "Linhas afetadas: 20", "duracao_ms": 3, "arquivo_csv": null}]', NULL, 88, 1, 1, 0, NULL, NULL, 20);
INSERT INTO public.tb_logs_execucao OVERRIDING SYSTEM VALUE VALUES (59, 37, '2026-03-19 13:36:17.102031-03', '2026-03-19 13:36:17.102031-03', 'sucesso', NULL, '[{"sql": "SELECT s.id, s.nome as setor FROM cfg_setor s ORDER BY s.nome", "erro": null, "tipo": "sql", "bloco": "bloco_1", "ordem": 1, "status": "sucesso", "id_bloco": 50, "registros": 5, "resultado": "Linhas afetadas: 5", "duracao_ms": 3, "arquivo_csv": null}]', NULL, 91, 1, 1, 0, NULL, NULL, 5);
INSERT INTO public.tb_logs_execucao OVERRIDING SYSTEM VALUE VALUES (60, 38, '2026-03-19 13:36:17.361007-03', '2026-03-19 13:36:17.361007-03', 'sucesso', NULL, '[{"sql": "SELECT * FROM audit_log ORDER BY id DESC LIMIT 15", "erro": null, "tipo": "sql", "bloco": "bloco_1", "ordem": 1, "status": "sucesso", "id_bloco": 62, "registros": 15, "resultado": "Linhas afetadas: 15", "duracao_ms": 4, "arquivo_csv": null}]', NULL, 91, 1, 1, 0, NULL, NULL, 15);
INSERT INTO public.tb_logs_execucao OVERRIDING SYSTEM VALUE VALUES (61, 39, '2026-03-19 13:36:17.553186-03', '2026-03-19 13:36:17.553186-03', 'falha', 'Não foi possível conectar ao banco alvo', '[]', NULL, 8, 0, 0, 0, NULL, NULL, 0);
INSERT INTO public.tb_logs_execucao OVERRIDING SYSTEM VALUE VALUES (62, 40, '2026-03-19 13:36:17.728201-03', '2026-03-19 13:36:17.728201-03', 'falha', 'Não foi possível conectar ao banco alvo', '[]', NULL, 19, 0, 0, 0, NULL, NULL, 0);
INSERT INTO public.tb_logs_execucao OVERRIDING SYSTEM VALUE VALUES (63, 44, '2026-03-19 13:36:17.895809-03', '2026-03-19 13:36:17.895809-03', 'falha', 'Não foi possível conectar ao banco alvo', '[]', NULL, 20, 0, 0, 0, NULL, NULL, 0);
INSERT INTO public.tb_logs_execucao OVERRIDING SYSTEM VALUE VALUES (64, 45, '2026-03-19 13:36:18.104355-03', '2026-03-19 13:36:18.104355-03', 'falha', 'Não foi possível conectar ao banco alvo', '[]', NULL, 20, 0, 0, 0, NULL, NULL, 0);
INSERT INTO public.tb_logs_execucao OVERRIDING SYSTEM VALUE VALUES (65, 46, '2026-03-19 13:36:18.302798-03', '2026-03-19 13:36:18.302798-03', 'falha', 'Não foi possível conectar ao banco alvo', '[]', NULL, 10, 0, 0, 0, NULL, NULL, 0);
INSERT INTO public.tb_logs_execucao OVERRIDING SYSTEM VALUE VALUES (66, 32, '2026-03-19 13:36:22.508045-03', '2026-03-19 13:36:22.508045-03', 'sucesso', NULL, '[{"sql": "SELECT id, titulo, id_status, data_abertura FROM tb_chamado ORDER BY data_abertura DESC LIMIT 10", "erro": "SQLSTATE[42703]: Undefined column: 7 ERRO:  coluna \"data_abertura\" não existe\nLINE 1: SELECT id, titulo, id_status, data_abertura FROM tb_chamado ...\n                                      ^", "tipo": "sql", "bloco": "bloco_1", "ordem": 1, "status": "falha", "id_bloco": 60, "registros": 0, "resultado": "", "duracao_ms": 1, "arquivo_csv": null}]', NULL, 82, 1, 0, 1, NULL, NULL, 0);
INSERT INTO public.tb_logs_execucao OVERRIDING SYSTEM VALUE VALUES (67, 32, '2026-03-19 13:36:31.006784-03', '2026-03-19 13:36:31.006784-03', 'sucesso', NULL, '[{"sql": "SELECT * FROM tb_chamado ORDER BY id DESC LIMIT 10", "erro": null, "tipo": "sql", "bloco": "bloco_1", "ordem": 1, "status": "sucesso", "id_bloco": 63, "registros": 4, "resultado": "Linhas afetadas: 4", "duracao_ms": 6, "arquivo_csv": null}]', NULL, 109, 1, 1, 0, NULL, NULL, 4);
INSERT INTO public.tb_logs_execucao OVERRIDING SYSTEM VALUE VALUES (68, 32, '2026-03-19 13:36:34.434973-03', '2026-03-19 13:36:34.434973-03', 'sucesso', NULL, '[{"sql": "SELECT * FROM tb_chamado ORDER BY id DESC LIMIT 10", "erro": null, "tipo": "sql", "bloco": "bloco_1", "ordem": 1, "status": "sucesso", "id_bloco": 63, "registros": 4, "resultado": "Linhas afetadas: 4", "duracao_ms": 5, "arquivo_csv": null}]', NULL, 109, 1, 1, 0, NULL, NULL, 4);
INSERT INTO public.tb_logs_execucao OVERRIDING SYSTEM VALUE VALUES (69, 46, '2026-03-19 14:38:54.720809-03', '2026-03-19 14:38:54.720809-03', 'falha', 'Não foi possível conectar ao banco alvo', '[]', NULL, 23, 0, 0, 0, NULL, NULL, 0);
INSERT INTO public.tb_logs_execucao OVERRIDING SYSTEM VALUE VALUES (70, 46, '2026-03-19 14:38:54.882388-03', '2026-03-19 14:38:54.882388-03', 'falha', 'Não foi possível conectar ao banco alvo', '[]', NULL, 8, 0, 0, 0, NULL, NULL, 0);
INSERT INTO public.tb_logs_execucao OVERRIDING SYSTEM VALUE VALUES (71, 46, '2026-03-19 14:38:55.094765-03', '2026-03-19 14:38:55.094765-03', 'falha', 'Não foi possível conectar ao banco alvo', '[]', NULL, 27, 0, 0, 0, NULL, NULL, 0);
INSERT INTO public.tb_logs_execucao OVERRIDING SYSTEM VALUE VALUES (72, 46, '2026-03-19 14:38:55.309676-03', '2026-03-19 14:38:55.309676-03', 'falha', 'Não foi possível conectar ao banco alvo', '[]', NULL, 26, 0, 0, 0, NULL, NULL, 0);
INSERT INTO public.tb_logs_execucao OVERRIDING SYSTEM VALUE VALUES (73, 46, '2026-03-19 14:38:55.528061-03', '2026-03-19 14:38:55.528061-03', 'falha', 'Não foi possível conectar ao banco alvo', '[]', NULL, 31, 0, 0, 0, NULL, NULL, 0);
INSERT INTO public.tb_logs_execucao OVERRIDING SYSTEM VALUE VALUES (74, 46, '2026-03-19 14:38:55.71417-03', '2026-03-19 14:38:55.71417-03', 'falha', 'Não foi possível conectar ao banco alvo', '[]', NULL, 18, 0, 0, 0, NULL, NULL, 0);
INSERT INTO public.tb_logs_execucao OVERRIDING SYSTEM VALUE VALUES (75, 46, '2026-03-19 14:38:55.915279-03', '2026-03-19 14:38:55.915279-03', 'falha', 'Não foi possível conectar ao banco alvo', '[]', NULL, 19, 0, 0, 0, NULL, NULL, 0);
INSERT INTO public.tb_logs_execucao OVERRIDING SYSTEM VALUE VALUES (76, 46, '2026-03-19 14:38:56.120527-03', '2026-03-19 14:38:56.120527-03', 'falha', 'Não foi possível conectar ao banco alvo', '[]', NULL, 23, 0, 0, 0, NULL, NULL, 0);
INSERT INTO public.tb_logs_execucao OVERRIDING SYSTEM VALUE VALUES (77, 46, '2026-03-19 14:38:56.316077-03', '2026-03-19 14:38:56.316077-03', 'falha', 'Não foi possível conectar ao banco alvo', '[]', NULL, 30, 0, 0, 0, NULL, NULL, 0);
INSERT INTO public.tb_logs_execucao OVERRIDING SYSTEM VALUE VALUES (78, 46, '2026-03-19 14:38:56.495211-03', '2026-03-19 14:38:56.495211-03', 'falha', 'Não foi possível conectar ao banco alvo', '[]', NULL, 7, 0, 0, 0, NULL, NULL, 0);
INSERT INTO public.tb_logs_execucao OVERRIDING SYSTEM VALUE VALUES (79, 46, '2026-03-19 15:41:03.709063-03', '2026-03-19 15:41:03.709063-03', 'falha', 'SQLSTATE[42000]: Syntax error or access violation: 1064 You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near ''PRIMARY KEY, id_rotina BIGINT, bloco_codigo TEXT, data_execucao TIMESTAMPTZ, ...'' at line 1', '[]', NULL, 23, 0, 0, 0, NULL, NULL, 0);
INSERT INTO public.tb_logs_execucao OVERRIDING SYSTEM VALUE VALUES (80, 46, '2026-03-19 15:41:42.525374-03', '2026-03-19 15:41:42.525374-03', 'sucesso', NULL, '[{"sql": "SELECT r.id, r.name as role_name, r.guard_name FROM roles r ORDER BY r.id", "erro": null, "tipo": "sql", "bloco": "bloco_1", "ordem": 1, "status": "sucesso", "id_bloco": 59, "registros": 5, "resultado": "Linhas afetadas: 5", "duracao_ms": 5, "arquivo_csv": null}]', NULL, 45, 1, 1, 0, NULL, NULL, 5);
INSERT INTO public.tb_logs_execucao OVERRIDING SYSTEM VALUE VALUES (81, 39, '2026-03-19 15:42:02.693504-03', '2026-03-19 15:42:02.693504-03', 'sucesso', NULL, '[{"sql": "SELECT * FROM tb_cfg_apartamentos LIMIT 20", "erro": null, "tipo": "sql", "bloco": "bloco_1", "ordem": 1, "status": "sucesso", "id_bloco": 52, "registros": 12, "resultado": "Linhas afetadas: 12", "duracao_ms": 4, "arquivo_csv": null}]', NULL, 24, 1, 1, 0, NULL, NULL, 12);
INSERT INTO public.tb_logs_execucao OVERRIDING SYSTEM VALUE VALUES (82, 40, '2026-03-19 15:42:02.898291-03', '2026-03-19 15:42:02.898291-03', 'sucesso', NULL, '[{"sql": "SELECT * FROM tb_avisos ORDER BY id DESC LIMIT 15", "erro": null, "tipo": "sql", "bloco": "bloco_1", "ordem": 1, "status": "sucesso", "id_bloco": 53, "registros": 0, "resultado": "Linhas afetadas: 0", "duracao_ms": 4, "arquivo_csv": null}]', NULL, 24, 1, 1, 0, NULL, NULL, 0);
INSERT INTO public.tb_logs_execucao OVERRIDING SYSTEM VALUE VALUES (83, 41, '2026-03-19 15:42:03.110302-03', '2026-03-19 15:42:03.110302-03', 'sucesso', NULL, '[{"sql": "SELECT * FROM tb_cfg_areas_comuns LIMIT 20", "erro": null, "tipo": "sql", "bloco": "bloco_1", "ordem": 1, "status": "sucesso", "id_bloco": 54, "registros": 0, "resultado": "Linhas afetadas: 0", "duracao_ms": 4, "arquivo_csv": null}]', NULL, 14, 1, 1, 0, NULL, NULL, 0);
INSERT INTO public.tb_logs_execucao OVERRIDING SYSTEM VALUE VALUES (84, 42, '2026-03-19 15:42:03.330518-03', '2026-03-19 15:42:03.330518-03', 'sucesso', NULL, '[{"sql": "SELECT * FROM tb_anuncios ORDER BY id DESC LIMIT 15", "erro": null, "tipo": "sql", "bloco": "bloco_1", "ordem": 1, "status": "sucesso", "id_bloco": 55, "registros": 0, "resultado": "Linhas afetadas: 0", "duracao_ms": 2, "arquivo_csv": null}]', NULL, 12, 1, 1, 0, NULL, NULL, 0);
INSERT INTO public.tb_logs_execucao OVERRIDING SYSTEM VALUE VALUES (85, 43, '2026-03-19 15:42:03.799506-03', '2026-03-19 15:42:03.799506-03', 'sucesso', NULL, '[{"sql": "SELECT r.id, r.name as role_name, r.guard_name FROM roles r ORDER BY r.id", "erro": null, "tipo": "sql", "bloco": "bloco_1", "ordem": 1, "status": "sucesso", "id_bloco": 56, "registros": 5, "resultado": "Linhas afetadas: 5", "duracao_ms": 9, "arquivo_csv": null}]', NULL, 47, 1, 1, 0, NULL, NULL, 5);
INSERT INTO public.tb_logs_execucao OVERRIDING SYSTEM VALUE VALUES (86, 44, '2026-03-19 15:42:04.17677-03', '2026-03-19 15:42:04.17677-03', 'sucesso', NULL, '[{"sql": "SELECT * FROM tb_cfg_areas_comuns LIMIT 20", "erro": null, "tipo": "sql", "bloco": "bloco_1", "ordem": 1, "status": "sucesso", "id_bloco": 57, "registros": 0, "resultado": "Linhas afetadas: 0", "duracao_ms": 3, "arquivo_csv": null}]', NULL, 27, 1, 1, 0, NULL, NULL, 0);
INSERT INTO public.tb_logs_execucao OVERRIDING SYSTEM VALUE VALUES (87, 45, '2026-03-19 15:42:04.427362-03', '2026-03-19 15:42:04.427362-03', 'sucesso', NULL, '[{"sql": "SELECT * FROM tb_anuncios ORDER BY id DESC LIMIT 15", "erro": null, "tipo": "sql", "bloco": "bloco_1", "ordem": 1, "status": "sucesso", "id_bloco": 58, "registros": 0, "resultado": "Linhas afetadas: 0", "duracao_ms": 3, "arquivo_csv": null}]', NULL, 28, 1, 1, 0, NULL, NULL, 0);
INSERT INTO public.tb_logs_execucao OVERRIDING SYSTEM VALUE VALUES (88, 39, '2026-03-19 15:42:34.055207-03', '2026-03-19 15:42:34.055207-03', 'sucesso', NULL, '[{"sql": "SELECT * FROM tb_cfg_apartamentos LIMIT 20", "erro": null, "tipo": "sql", "bloco": "bloco_1", "ordem": 1, "status": "sucesso", "id_bloco": 52, "registros": 12, "resultado": "Linhas afetadas: 12", "duracao_ms": 3, "arquivo_csv": null}]', NULL, 21, 1, 1, 0, NULL, NULL, 12);
INSERT INTO public.tb_logs_execucao OVERRIDING SYSTEM VALUE VALUES (89, 40, '2026-03-19 15:42:34.276311-03', '2026-03-19 15:42:34.276311-03', 'sucesso', NULL, '[{"sql": "SELECT * FROM tb_avisos ORDER BY id DESC LIMIT 15", "erro": null, "tipo": "sql", "bloco": "bloco_1", "ordem": 1, "status": "sucesso", "id_bloco": 53, "registros": 0, "resultado": "Linhas afetadas: 0", "duracao_ms": 2, "arquivo_csv": null}]', NULL, 36, 1, 1, 0, NULL, NULL, 0);
INSERT INTO public.tb_logs_execucao OVERRIDING SYSTEM VALUE VALUES (90, 41, '2026-03-19 15:42:34.495179-03', '2026-03-19 15:42:34.495179-03', 'sucesso', NULL, '[{"sql": "SELECT * FROM tb_cfg_areas_comuns LIMIT 20", "erro": null, "tipo": "sql", "bloco": "bloco_1", "ordem": 1, "status": "sucesso", "id_bloco": 54, "registros": 0, "resultado": "Linhas afetadas: 0", "duracao_ms": 3, "arquivo_csv": null}]', NULL, 13, 1, 1, 0, NULL, NULL, 0);
INSERT INTO public.tb_logs_execucao OVERRIDING SYSTEM VALUE VALUES (91, 42, '2026-03-19 15:42:34.777708-03', '2026-03-19 15:42:34.777708-03', 'sucesso', NULL, '[{"sql": "SELECT * FROM tb_anuncios ORDER BY id DESC LIMIT 15", "erro": null, "tipo": "sql", "bloco": "bloco_1", "ordem": 1, "status": "sucesso", "id_bloco": 55, "registros": 0, "resultado": "Linhas afetadas: 0", "duracao_ms": 2, "arquivo_csv": null}]', NULL, 28, 1, 1, 0, NULL, NULL, 0);
INSERT INTO public.tb_logs_execucao OVERRIDING SYSTEM VALUE VALUES (92, 43, '2026-03-19 15:42:34.983719-03', '2026-03-19 15:42:34.983719-03', 'sucesso', NULL, '[{"sql": "SELECT r.id, r.name as role_name, r.guard_name FROM roles r ORDER BY r.id", "erro": null, "tipo": "sql", "bloco": "bloco_1", "ordem": 1, "status": "sucesso", "id_bloco": 56, "registros": 5, "resultado": "Linhas afetadas: 5", "duracao_ms": 3, "arquivo_csv": null}]', NULL, 12, 1, 1, 0, NULL, NULL, 5);
INSERT INTO public.tb_logs_execucao OVERRIDING SYSTEM VALUE VALUES (93, 44, '2026-03-19 15:42:35.204567-03', '2026-03-19 15:42:35.204567-03', 'sucesso', NULL, '[{"sql": "SELECT * FROM tb_cfg_areas_comuns LIMIT 20", "erro": null, "tipo": "sql", "bloco": "bloco_1", "ordem": 1, "status": "sucesso", "id_bloco": 57, "registros": 0, "resultado": "Linhas afetadas: 0", "duracao_ms": 3, "arquivo_csv": null}]', NULL, 21, 1, 1, 0, NULL, NULL, 0);
INSERT INTO public.tb_logs_execucao OVERRIDING SYSTEM VALUE VALUES (94, 45, '2026-03-19 15:42:35.401566-03', '2026-03-19 15:42:35.401566-03', 'sucesso', NULL, '[{"sql": "SELECT * FROM tb_anuncios ORDER BY id DESC LIMIT 15", "erro": null, "tipo": "sql", "bloco": "bloco_1", "ordem": 1, "status": "sucesso", "id_bloco": 58, "registros": 0, "resultado": "Linhas afetadas: 0", "duracao_ms": 2, "arquivo_csv": null}]', NULL, 11, 1, 1, 0, NULL, NULL, 0);
INSERT INTO public.tb_logs_execucao OVERRIDING SYSTEM VALUE VALUES (95, NULL, '2026-03-19 15:53:08.007243-03', '2026-03-19 15:53:08.007243-03', 'sucesso', NULL, '[]', NULL, 14, 0, 0, 0, NULL, NULL, 0);
INSERT INTO public.tb_logs_execucao OVERRIDING SYSTEM VALUE VALUES (96, 34, '2026-03-20 08:00:05-03', '2026-03-20 08:00:08-03', 'sucesso', NULL, NULL, NULL, 3200, 0, 0, 0, NULL, NULL, 42);
INSERT INTO public.tb_logs_execucao OVERRIDING SYSTEM VALUE VALUES (97, 33, '2026-03-20 07:30:01-03', '2026-03-20 07:30:04-03', 'sucesso', NULL, NULL, NULL, 2800, 0, 0, 0, NULL, NULL, 156);
INSERT INTO public.tb_logs_execucao OVERRIDING SYSTEM VALUE VALUES (98, 49, '2026-03-19 07:00:02-03', '2026-03-19 07:00:06-03', 'sucesso', NULL, NULL, NULL, 4100, 0, 0, 0, NULL, NULL, 310);
INSERT INTO public.tb_logs_execucao OVERRIDING SYSTEM VALUE VALUES (99, 50, '2026-03-19 12:00:01-03', '2026-03-19 12:00:03-03', 'sucesso', NULL, NULL, NULL, 1900, 0, 0, 0, NULL, NULL, 87);
INSERT INTO public.tb_logs_execucao OVERRIDING SYSTEM VALUE VALUES (100, 54, '2026-03-18 06:00:03-03', '2026-03-18 06:00:09-03', 'sucesso', NULL, NULL, NULL, 5600, 0, 0, 0, NULL, NULL, 523);
INSERT INTO public.tb_logs_execucao OVERRIDING SYSTEM VALUE VALUES (101, 63, '2026-03-18 08:00:01-03', '2026-03-18 08:00:04-03', 'sucesso', NULL, NULL, NULL, 3100, 0, 0, 0, NULL, NULL, 28);
INSERT INTO public.tb_logs_execucao OVERRIDING SYSTEM VALUE VALUES (102, 34, '2026-03-18 08:00:01-03', '2026-03-18 08:00:02-03', 'falha', NULL, NULL, NULL, 800, 0, 0, 0, NULL, NULL, 0);
INSERT INTO public.tb_logs_execucao OVERRIDING SYSTEM VALUE VALUES (103, 33, '2026-03-18 07:00:01-03', '2026-03-18 07:00:03-03', 'sucesso', NULL, NULL, NULL, 2200, 0, 0, 0, NULL, NULL, 148);
INSERT INTO public.tb_logs_execucao OVERRIDING SYSTEM VALUE VALUES (104, 57, '2026-03-20 06:10:01-03', '2026-03-20 06:10:03-03', 'sucesso', NULL, NULL, NULL, 2400, 0, 0, 0, NULL, NULL, 15);
INSERT INTO public.tb_logs_execucao OVERRIDING SYSTEM VALUE VALUES (105, 57, '2026-03-19 06:10:01-03', '2026-03-19 06:10:04-03', 'sucesso', NULL, NULL, NULL, 2900, 0, 0, 0, NULL, NULL, 15);
INSERT INTO public.tb_logs_execucao OVERRIDING SYSTEM VALUE VALUES (106, 58, '2026-03-19 10:00:02-03', '2026-03-19 10:00:05-03', 'sucesso', NULL, NULL, NULL, 3200, 0, 0, 0, NULL, NULL, 8);
INSERT INTO public.tb_logs_execucao OVERRIDING SYSTEM VALUE VALUES (107, 57, '2026-03-18 06:10:01-03', '2026-03-18 06:10:02-03', 'falha', NULL, NULL, NULL, 900, 0, 0, 0, NULL, NULL, 0);
INSERT INTO public.tb_logs_execucao OVERRIDING SYSTEM VALUE VALUES (108, 58, '2026-03-18 10:00:01-03', '2026-03-18 10:00:04-03', 'sucesso', NULL, NULL, NULL, 2800, 0, 0, 0, NULL, NULL, 7);
INSERT INTO public.tb_logs_execucao OVERRIDING SYSTEM VALUE VALUES (109, 60, '2026-03-20 05:00:02-03', '2026-03-20 05:00:07-03', 'sucesso', NULL, NULL, NULL, 4800, 0, 0, 0, NULL, NULL, 890);
INSERT INTO public.tb_logs_execucao OVERRIDING SYSTEM VALUE VALUES (110, 61, '2026-03-20 08:00:01-03', '2026-03-20 08:00:04-03', 'sucesso', NULL, NULL, NULL, 3100, 0, 0, 0, NULL, NULL, 234);
INSERT INTO public.tb_logs_execucao OVERRIDING SYSTEM VALUE VALUES (111, 60, '2026-03-19 05:00:01-03', '2026-03-19 05:00:06-03', 'sucesso', NULL, NULL, NULL, 5200, 0, 0, 0, NULL, NULL, 856);
INSERT INTO public.tb_logs_execucao OVERRIDING SYSTEM VALUE VALUES (112, 61, '2026-03-19 14:00:02-03', '2026-03-19 14:00:05-03', 'sucesso', NULL, NULL, NULL, 2700, 0, 0, 0, NULL, NULL, 198);
INSERT INTO public.tb_logs_execucao OVERRIDING SYSTEM VALUE VALUES (113, 61, '2026-03-18 20:00:01-03', '2026-03-18 20:00:02-03', 'falha', NULL, NULL, NULL, 600, 0, 0, 0, NULL, NULL, 0);


--
-- TOC entry 5449 (class 0 OID 45632)
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
INSERT INTO public.tb_logs_sistema VALUES (16, 'INFO', 'Evento criado: TEST_EVENTO_1', '{"id": 8}', 'app', NULL, '::1', 'Mozilla/5.0 (Windows NT; Windows NT 10.0; pt-BR) WindowsPowerShell/5.1.26100.6584', '/DMC-DATALOAD/public/api/eventos-api/salvar', NULL, NULL, '2026-03-19 10:43:00.575746-03');
INSERT INTO public.tb_logs_sistema VALUES (17, 'INFO', 'Evento atualizado: TEST_EVENTO_1', '{"id": 8}', 'app', NULL, '::1', 'Mozilla/5.0 (Windows NT; Windows NT 10.0; pt-BR) WindowsPowerShell/5.1.26100.6584', '/DMC-DATALOAD/public/api/eventos-api/salvar', NULL, NULL, '2026-03-19 10:43:44.935506-03');
INSERT INTO public.tb_logs_sistema VALUES (18, 'INFO', 'Evento atualizado: TEST_EVENTO_1', '{"id": 8}', 'app', NULL, '::1', 'Mozilla/5.0 (Windows NT; Windows NT 10.0; pt-BR) WindowsPowerShell/5.1.26100.6584', '/DMC-DATALOAD/public/api/eventos-api/salvar', NULL, NULL, '2026-03-19 10:45:50.326357-03');
INSERT INTO public.tb_logs_sistema VALUES (19, 'WARNING', 'Evento excluído ID: 8', '{"id": 8}', 'app', NULL, '::1', 'Mozilla/5.0 (Windows NT; Windows NT 10.0; pt-BR) WindowsPowerShell/5.1.26100.6584', '/DMC-DATALOAD/public/api/eventos-api/delete/8', NULL, NULL, '2026-03-19 10:51:00.138827-03');
INSERT INTO public.tb_logs_sistema VALUES (20, 'WARNING', 'Evento excluído ID: 8', '{"id": 8}', 'app', NULL, '::1', 'Mozilla/5.0 (Windows NT; Windows NT 10.0; pt-BR) WindowsPowerShell/5.1.26100.6584', '/DMC-DATALOAD/public/api/eventos-api/delete/8', NULL, NULL, '2026-03-19 10:51:06.899542-03');
INSERT INTO public.tb_logs_sistema VALUES (21, 'WARNING', 'API excluída ID: 17', '{"id": 17}', 'app', NULL, '::1', 'Mozilla/5.0 (Windows NT; Windows NT 10.0; pt-BR) WindowsPowerShell/5.1.26100.6584', '/DMC-DATALOAD/public/api/apis-externas/delete/17', NULL, NULL, '2026-03-19 10:51:13.295666-03');
INSERT INTO public.tb_logs_sistema VALUES (22, 'INFO', 'API criada: API_JSONPlaceholder_Posts', '{"id": 18}', 'app', NULL, '::1', 'Mozilla/5.0 (Windows NT; Windows NT 10.0; pt-BR) WindowsPowerShell/5.1.26100.6584', '/DMC-DATALOAD/public/api/apis-externas/salvar', NULL, NULL, '2026-03-19 13:33:54.591155-03');
INSERT INTO public.tb_logs_sistema VALUES (23, 'INFO', 'API criada: API_JSONPlaceholder_Users', '{"id": 19}', 'app', NULL, '::1', 'Mozilla/5.0 (Windows NT; Windows NT 10.0; pt-BR) WindowsPowerShell/5.1.26100.6584', '/DMC-DATALOAD/public/api/apis-externas/salvar', NULL, NULL, '2026-03-19 13:34:01.095962-03');
INSERT INTO public.tb_logs_sistema VALUES (24, 'INFO', 'API criada: API_JSONPlaceholder_Comments', '{"id": 20}', 'app', NULL, '::1', 'Mozilla/5.0 (Windows NT; Windows NT 10.0; pt-BR) WindowsPowerShell/5.1.26100.6584', '/DMC-DATALOAD/public/api/apis-externas/salvar', NULL, NULL, '2026-03-19 13:34:04.549469-03');
INSERT INTO public.tb_logs_sistema VALUES (25, 'INFO', 'API criada: API_JSONPlaceholder_Todos', '{"id": 21}', 'app', NULL, '::1', 'Mozilla/5.0 (Windows NT; Windows NT 10.0; pt-BR) WindowsPowerShell/5.1.26100.6584', '/DMC-DATALOAD/public/api/apis-externas/salvar', NULL, NULL, '2026-03-19 13:34:04.753061-03');
INSERT INTO public.tb_logs_sistema VALUES (26, 'INFO', 'API criada: API_JSONPlaceholder_Albums', '{"id": 22}', 'app', NULL, '::1', 'Mozilla/5.0 (Windows NT; Windows NT 10.0; pt-BR) WindowsPowerShell/5.1.26100.6584', '/DMC-DATALOAD/public/api/apis-externas/salvar', NULL, NULL, '2026-03-19 13:34:05.031717-03');
INSERT INTO public.tb_logs_sistema VALUES (27, 'INFO', 'API criada: API_JSONPlaceholder_Comments', '{"id": 23}', 'app', NULL, '::1', 'Mozilla/5.0 (Windows NT; Windows NT 10.0; pt-BR) WindowsPowerShell/5.1.26100.6584', '/DMC-DATALOAD/public/api/apis-externas/salvar', NULL, NULL, '2026-03-19 13:34:07.982799-03');
INSERT INTO public.tb_logs_sistema VALUES (28, 'INFO', 'API criada: API_JSONPlaceholder_Todos', '{"id": 24}', 'app', NULL, '::1', 'Mozilla/5.0 (Windows NT; Windows NT 10.0; pt-BR) WindowsPowerShell/5.1.26100.6584', '/DMC-DATALOAD/public/api/apis-externas/salvar', NULL, NULL, '2026-03-19 13:34:08.104343-03');
INSERT INTO public.tb_logs_sistema VALUES (29, 'INFO', 'API criada: API_JSONPlaceholder_Albums', '{"id": 25}', 'app', NULL, '::1', 'Mozilla/5.0 (Windows NT; Windows NT 10.0; pt-BR) WindowsPowerShell/5.1.26100.6584', '/DMC-DATALOAD/public/api/apis-externas/salvar', NULL, NULL, '2026-03-19 13:34:08.231738-03');
INSERT INTO public.tb_logs_sistema VALUES (30, 'INFO', 'Evento criado: Evento_Posts_UserID_1', '{"id": 9}', 'app', NULL, '::1', 'Mozilla/5.0 (Windows NT; Windows NT 10.0; pt-BR) WindowsPowerShell/5.1.26100.6584', '/DMC-DATALOAD/public/api/eventos-api/salvar', NULL, NULL, '2026-03-19 13:34:16.780386-03');
INSERT INTO public.tb_logs_sistema VALUES (31, 'INFO', 'Evento criado: Evento_Users_Count', '{"id": 10}', 'app', NULL, '::1', 'Mozilla/5.0 (Windows NT; Windows NT 10.0; pt-BR) WindowsPowerShell/5.1.26100.6584', '/DMC-DATALOAD/public/api/eventos-api/salvar', NULL, NULL, '2026-03-19 13:34:23.768073-03');
INSERT INTO public.tb_logs_sistema VALUES (32, 'INFO', 'Evento criado: Evento_Comments_Email', '{"id": 11}', 'app', NULL, '::1', 'Mozilla/5.0 (Windows NT; Windows NT 10.0; pt-BR) WindowsPowerShell/5.1.26100.6584', '/DMC-DATALOAD/public/api/eventos-api/salvar', NULL, NULL, '2026-03-19 13:34:23.944273-03');
INSERT INTO public.tb_logs_sistema VALUES (33, 'INFO', 'Evento criado: Evento_Todos_Completed', '{"id": 12}', 'app', NULL, '::1', 'Mozilla/5.0 (Windows NT; Windows NT 10.0; pt-BR) WindowsPowerShell/5.1.26100.6584', '/DMC-DATALOAD/public/api/eventos-api/salvar', NULL, NULL, '2026-03-19 13:34:24.108616-03');
INSERT INTO public.tb_logs_sistema VALUES (34, 'INFO', 'Evento criado: Evento_Albums_Title', '{"id": 13}', 'app', NULL, '::1', 'Mozilla/5.0 (Windows NT; Windows NT 10.0; pt-BR) WindowsPowerShell/5.1.26100.6584', '/DMC-DATALOAD/public/api/eventos-api/salvar', NULL, NULL, '2026-03-19 13:34:24.281228-03');
INSERT INTO public.tb_logs_sistema VALUES (35, 'info', 'Agendamento configurado para rotina ID 34: */30 * * * *', '{}', 'scheduler', 1, NULL, NULL, NULL, NULL, NULL, '2026-03-19 13:37:31.757367-03');
INSERT INTO public.tb_logs_sistema VALUES (36, 'info', 'Agendamento configurado para rotina ID 37: 0 9 * * *', '{}', 'scheduler', 1, NULL, NULL, NULL, NULL, NULL, '2026-03-19 13:37:32.042902-03');
INSERT INTO public.tb_logs_sistema VALUES (37, 'info', 'Agendamento configurado para rotina ID 33: 0 * * * *', '{}', 'scheduler', 1, NULL, NULL, NULL, NULL, NULL, '2026-03-19 13:37:32.376797-03');


--
-- TOC entry 5451 (class 0 OID 45647)
-- Dependencies: 238
-- Data for Name: tb_metricas_sistema; Type: TABLE DATA; Schema: public; Owner: postgres
--



--
-- TOC entry 5477 (class 0 OID 46308)
-- Dependencies: 265
-- Data for Name: tb_notificacoes; Type: TABLE DATA; Schema: public; Owner: postgres
--

INSERT INTO public.tb_notificacoes VALUES (2, 'rotina_falha', 'Falha na execução: MY_Teste_05_Roles_Permissions', 'A rotina "MY_Teste_05_Roles_Permissions" (ID: 46) falhou: Não foi possível conectar ao banco alvo', '{"erro": "Não foi possível conectar ao banco alvo", "nome": "MY_Teste_05_Roles_Permissions", "id_rotina": 46, "duracao_ms": 23, "blocos_falha": 0}', false, NULL, '2026-03-19 14:38:54.724276');
INSERT INTO public.tb_notificacoes VALUES (3, 'rotina_falha', 'Falha na execução: MY_Teste_05_Roles_Permissions', 'A rotina "MY_Teste_05_Roles_Permissions" (ID: 46) falhou: Não foi possível conectar ao banco alvo', '{"erro": "Não foi possível conectar ao banco alvo", "nome": "MY_Teste_05_Roles_Permissions", "id_rotina": 46, "duracao_ms": 8, "blocos_falha": 0}', false, NULL, '2026-03-19 14:38:54.885256');
INSERT INTO public.tb_notificacoes VALUES (4, 'rotina_falha', 'Falha na execução: MY_Teste_05_Roles_Permissions', 'A rotina "MY_Teste_05_Roles_Permissions" (ID: 46) falhou: Não foi possível conectar ao banco alvo', '{"erro": "Não foi possível conectar ao banco alvo", "nome": "MY_Teste_05_Roles_Permissions", "id_rotina": 46, "duracao_ms": 27, "blocos_falha": 0}', false, NULL, '2026-03-19 14:38:55.097487');
INSERT INTO public.tb_notificacoes VALUES (5, 'rotina_falha', 'Falha na execução: MY_Teste_05_Roles_Permissions', 'A rotina "MY_Teste_05_Roles_Permissions" (ID: 46) falhou: Não foi possível conectar ao banco alvo', '{"erro": "Não foi possível conectar ao banco alvo", "nome": "MY_Teste_05_Roles_Permissions", "id_rotina": 46, "duracao_ms": 26, "blocos_falha": 0}', false, NULL, '2026-03-19 14:38:55.312964');
INSERT INTO public.tb_notificacoes VALUES (6, 'rotina_falha', 'Falha na execução: MY_Teste_05_Roles_Permissions', 'A rotina "MY_Teste_05_Roles_Permissions" (ID: 46) falhou: Não foi possível conectar ao banco alvo', '{"erro": "Não foi possível conectar ao banco alvo", "nome": "MY_Teste_05_Roles_Permissions", "id_rotina": 46, "duracao_ms": 31, "blocos_falha": 0}', false, NULL, '2026-03-19 14:38:55.530488');
INSERT INTO public.tb_notificacoes VALUES (7, 'rotina_falha', 'Falha na execução: MY_Teste_05_Roles_Permissions', 'A rotina "MY_Teste_05_Roles_Permissions" (ID: 46) falhou: Não foi possível conectar ao banco alvo', '{"erro": "Não foi possível conectar ao banco alvo", "nome": "MY_Teste_05_Roles_Permissions", "id_rotina": 46, "duracao_ms": 18, "blocos_falha": 0}', false, NULL, '2026-03-19 14:38:55.717041');
INSERT INTO public.tb_notificacoes VALUES (8, 'rotina_falha', 'Falha na execução: MY_Teste_05_Roles_Permissions', 'A rotina "MY_Teste_05_Roles_Permissions" (ID: 46) falhou: Não foi possível conectar ao banco alvo', '{"erro": "Não foi possível conectar ao banco alvo", "nome": "MY_Teste_05_Roles_Permissions", "id_rotina": 46, "duracao_ms": 19, "blocos_falha": 0}', false, NULL, '2026-03-19 14:38:55.917883');
INSERT INTO public.tb_notificacoes VALUES (9, 'rotina_falha', 'Falha na execução: MY_Teste_05_Roles_Permissions', 'A rotina "MY_Teste_05_Roles_Permissions" (ID: 46) falhou: Não foi possível conectar ao banco alvo', '{"erro": "Não foi possível conectar ao banco alvo", "nome": "MY_Teste_05_Roles_Permissions", "id_rotina": 46, "duracao_ms": 23, "blocos_falha": 0}', false, NULL, '2026-03-19 14:38:56.123408');
INSERT INTO public.tb_notificacoes VALUES (10, 'rotina_falha', 'Falha na execução: MY_Teste_05_Roles_Permissions', 'A rotina "MY_Teste_05_Roles_Permissions" (ID: 46) falhou: Não foi possível conectar ao banco alvo', '{"erro": "Não foi possível conectar ao banco alvo", "nome": "MY_Teste_05_Roles_Permissions", "id_rotina": 46, "duracao_ms": 30, "blocos_falha": 0}', false, NULL, '2026-03-19 14:38:56.318525');
INSERT INTO public.tb_notificacoes VALUES (11, 'rotina_falha', 'Falha na execução: MY_Teste_05_Roles_Permissions', 'A rotina "MY_Teste_05_Roles_Permissions" (ID: 46) falhou: Não foi possível conectar ao banco alvo', '{"erro": "Não foi possível conectar ao banco alvo", "nome": "MY_Teste_05_Roles_Permissions", "id_rotina": 46, "duracao_ms": 7, "blocos_falha": 0}', false, NULL, '2026-03-19 14:38:56.49771');
INSERT INTO public.tb_notificacoes VALUES (12, 'rotina_falha', 'Falha na execução: MY_Teste_05_Roles_Permissions', 'A rotina "MY_Teste_05_Roles_Permissions" (ID: 46) falhou: SQLSTATE[42000]: Syntax error or access violation: 1064 You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near ''PRIMARY KEY, id_rotina BIGINT, bloco_codigo TEXT, data_execucao TIMESTAMPTZ, ...'' at line 1', '{"erro": "SQLSTATE[42000]: Syntax error or access violation: 1064 You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near ''PRIMARY KEY, id_rotina BIGINT, bloco_codigo TEXT, data_execucao TIMESTAMPTZ, ...'' at line 1", "nome": "MY_Teste_05_Roles_Permissions", "id_rotina": 46, "duracao_ms": 23, "blocos_falha": 0}', false, NULL, '2026-03-19 15:41:03.713307');


--
-- TOC entry 5506 (class 0 OID 46602)
-- Dependencies: 294
-- Data for Name: tb_password_resets; Type: TABLE DATA; Schema: public; Owner: postgres
--

INSERT INTO public.tb_password_resets OVERRIDING SYSTEM VALUE VALUES (3, 13, '04e78d3db6a6f199f5cbc2771ea8504762ed5ab6ea46991660f40b30e9acdd4a', '0056FE', '2026-03-20 17:51:21.931395-03', '2026-03-20 18:21:21.931395-03', true);


--
-- TOC entry 5441 (class 0 OID 45561)
-- Dependencies: 228
-- Data for Name: tb_perfis_conexao; Type: TABLE DATA; Schema: public; Owner: postgres
--

INSERT INTO public.tb_perfis_conexao OVERRIDING SYSTEM VALUE VALUES (91, 'PG_Infra_Servidores', 'postgres', 'localhost', 5433, 'db_infra_test', 'postgres', 'test123', NULL, '2026-03-20 14:09:59.471965-03', 1);
INSERT INTO public.tb_perfis_conexao OVERRIDING SYSTEM VALUE VALUES (11, 'DMC-Movie', 'postgres', 'localhost', 5432, 'db_dmc_movie', 'postgres', 'WQ6t8jGJs2pkYhGGSeT47g==:l3qAw1dhbASmobZn7NAulw==', '[]', '2026-02-03 08:42:25.170637-03', 1);
INSERT INTO public.tb_perfis_conexao OVERRIDING SYSTEM VALUE VALUES (12, 'HOMOLOGAÇÃO - C_ERGON', 'oracle', '10.238.205.116', 1521, NULL, 'c_ergon', 'dxRalNP4R4y2P4ZWoaEoWA==:cnaJRHeklyu3HewGZG7WFA==', '{"sid": "SADRHPRO", "tipo_conexao_oracle": "sid"}', '2026-02-03 11:00:51.717945-03', 1);
INSERT INTO public.tb_perfis_conexao OVERRIDING SYSTEM VALUE VALUES (92, 'MY_Saude_Prontuarios', 'mysql', 'localhost', 3306, 'db_saude_test', 'root', 'test123', NULL, '2026-03-20 14:09:59.496033-03', 1);
INSERT INTO public.tb_perfis_conexao OVERRIDING SYSTEM VALUE VALUES (93, 'PG_Portal_Interno', 'postgres', 'localhost', 5433, 'db_portal_test', 'postgres', 'test123', NULL, '2026-03-20 14:09:59.515296-03', 1);
INSERT INTO public.tb_perfis_conexao OVERRIDING SYSTEM VALUE VALUES (14, 'HOMOLOGAÇÃO - DMC-Exam', 'mysql', 'localhost', 3306, 'db_dmc_exames', 'root', '8EpZ0fnE1NG/ZFBHZlW+Pg==:fTvyHOybkfXVZKkwi8BIlg==', '[]', '2026-02-03 11:08:55.41701-03', 1);
INSERT INTO public.tb_perfis_conexao OVERRIDING SYSTEM VALUE VALUES (16, 'MySQL_Condominio', 'mysql', 'localhost', 3306, 'db_dmc_condominio', 'root', 'LC7Jp5u2/d662ZEU3jUi+g==:SEerfpBHz3NzUHoJatHbtw==', '[]', '2026-03-19 13:19:27.936357-03', 1);
INSERT INTO public.tb_perfis_conexao OVERRIDING SYSTEM VALUE VALUES (15, 'PostgreSQL_Chamados', 'postgres', 'localhost', 5433, 'db_chamados', 'postgres', 'SaPPhsq3t5a3MnvnwFYoBQ==:NYow8dHerzhEASXBwB9UWw==', '[]', '2026-03-19 13:19:21.892152-03', 1);


--
-- TOC entry 5475 (class 0 OID 46278)
-- Dependencies: 263
-- Data for Name: tb_pipeline_execucoes; Type: TABLE DATA; Schema: public; Owner: postgres
--

INSERT INTO public.tb_pipeline_execucoes VALUES (2, 6, 'success', '2026-03-19 13:36:51.753795-03', '2026-03-19 13:36:51.755799-03', 0, 2, 2, 2, 0, '{"1": null, "2": null}', '[{"type": "rotina", "label": "rotina", "status": "success", "node_id": 1, "timestamp": "2026-03-19T17:36:51+01:00", "duration_ms": 0, "result_preview": "null"}, {"type": "rotina", "label": "rotina", "status": "success", "node_id": "2", "timestamp": "2026-03-19T17:36:51+01:00", "duration_ms": 0, "result_preview": "null"}]', NULL, 1);
INSERT INTO public.tb_pipeline_execucoes VALUES (3, 7, 'success', '2026-03-19 13:36:51.871139-03', '2026-03-19 13:36:51.873186-03', 0, 3, 3, 3, 0, '{"1": null, "2": null, "3": null}', '[{"type": "rotina", "label": "rotina", "status": "success", "node_id": 1, "timestamp": "2026-03-19T17:36:51+01:00", "duration_ms": 0, "result_preview": "null"}, {"type": "rotina", "label": "rotina", "status": "success", "node_id": "2", "timestamp": "2026-03-19T17:36:51+01:00", "duration_ms": 0, "result_preview": "null"}, {"type": "rotina", "label": "rotina", "status": "success", "node_id": "3", "timestamp": "2026-03-19T17:36:51+01:00", "duration_ms": 0, "result_preview": "null"}]', NULL, 1);
INSERT INTO public.tb_pipeline_execucoes VALUES (4, 8, 'success', '2026-03-19 13:36:52.014454-03', '2026-03-19 13:36:52.017378-03', 0, 2, 2, 2, 0, '{"1": null, "2": null}', '[{"type": "rotina", "label": "rotina", "status": "success", "node_id": 1, "timestamp": "2026-03-19T17:36:52+01:00", "duration_ms": 0, "result_preview": "null"}, {"type": "rotina", "label": "rotina", "status": "success", "node_id": "2", "timestamp": "2026-03-19T17:36:52+01:00", "duration_ms": 0, "result_preview": "null"}]', NULL, 1);
INSERT INTO public.tb_pipeline_execucoes VALUES (5, 10, 'success', '2026-03-19 13:36:52.31526-03', '2026-03-19 13:36:52.318216-03', 0, 3, 3, 3, 0, '{"1": null, "2": null, "3": null}', '[{"type": "rotina", "label": "rotina", "status": "success", "node_id": 1, "timestamp": "2026-03-19T17:36:52+01:00", "duration_ms": 0, "result_preview": "null"}, {"type": "rotina", "label": "rotina", "status": "success", "node_id": "2", "timestamp": "2026-03-19T17:36:52+01:00", "duration_ms": 0, "result_preview": "null"}, {"type": "rotina", "label": "rotina", "status": "success", "node_id": "3", "timestamp": "2026-03-19T17:36:52+01:00", "duration_ms": 0, "result_preview": "null"}]', NULL, 1);
INSERT INTO public.tb_pipeline_execucoes VALUES (6, 12, 'success', '2026-03-19 13:36:52.50121-03', '2026-03-19 13:36:52.503808-03', 0, 4, 4, 4, 0, '{"1": null, "2": null, "3": null, "4": null}', '[{"type": "rotina", "label": "rotina", "status": "success", "node_id": 1, "timestamp": "2026-03-19T17:36:52+01:00", "duration_ms": 0, "result_preview": "null"}, {"type": "rotina", "label": "rotina", "status": "success", "node_id": 2, "timestamp": "2026-03-19T17:36:52+01:00", "duration_ms": 0, "result_preview": "null"}, {"type": "rotina", "label": "rotina", "status": "success", "node_id": 3, "timestamp": "2026-03-19T17:36:52+01:00", "duration_ms": 0, "result_preview": "null"}, {"type": "rotina", "label": "rotina", "status": "success", "node_id": 4, "timestamp": "2026-03-19T17:36:52+01:00", "duration_ms": 0, "result_preview": "null"}]', NULL, 1);
INSERT INTO public.tb_pipeline_execucoes VALUES (7, 6, 'success', '2026-03-19 13:36:54.23667-03', '2026-03-19 13:36:54.239248-03', 0, 2, 2, 2, 0, '{"1": null, "2": null}', '[{"type": "rotina", "label": "rotina", "status": "success", "node_id": 1, "timestamp": "2026-03-19T17:36:54+01:00", "duration_ms": 0, "result_preview": "null"}, {"type": "rotina", "label": "rotina", "status": "success", "node_id": "2", "timestamp": "2026-03-19T17:36:54+01:00", "duration_ms": 0, "result_preview": "null"}]', NULL, 1);
INSERT INTO public.tb_pipeline_execucoes VALUES (8, 7, 'success', '2026-03-19 13:36:54.432707-03', '2026-03-19 13:36:54.435543-03', 0, 3, 3, 3, 0, '{"1": null, "2": null, "3": null}', '[{"type": "rotina", "label": "rotina", "status": "success", "node_id": 1, "timestamp": "2026-03-19T17:36:54+01:00", "duration_ms": 0, "result_preview": "null"}, {"type": "rotina", "label": "rotina", "status": "success", "node_id": "2", "timestamp": "2026-03-19T17:36:54+01:00", "duration_ms": 0, "result_preview": "null"}, {"type": "rotina", "label": "rotina", "status": "success", "node_id": "3", "timestamp": "2026-03-19T17:36:54+01:00", "duration_ms": 0, "result_preview": "null"}]', NULL, 1);
INSERT INTO public.tb_pipeline_execucoes VALUES (9, 8, 'success', '2026-03-19 13:36:54.64436-03', '2026-03-19 13:36:54.647079-03', 0, 2, 2, 2, 0, '{"1": null, "2": null}', '[{"type": "rotina", "label": "rotina", "status": "success", "node_id": 1, "timestamp": "2026-03-19T17:36:54+01:00", "duration_ms": 0, "result_preview": "null"}, {"type": "rotina", "label": "rotina", "status": "success", "node_id": "2", "timestamp": "2026-03-19T17:36:54+01:00", "duration_ms": 0, "result_preview": "null"}]', NULL, 1);
INSERT INTO public.tb_pipeline_execucoes VALUES (10, 10, 'success', '2026-03-19 13:36:54.858227-03', '2026-03-19 13:36:54.861423-03', 0, 3, 3, 3, 0, '{"1": null, "2": null, "3": null}', '[{"type": "rotina", "label": "rotina", "status": "success", "node_id": 1, "timestamp": "2026-03-19T17:36:54+01:00", "duration_ms": 0, "result_preview": "null"}, {"type": "rotina", "label": "rotina", "status": "success", "node_id": "2", "timestamp": "2026-03-19T17:36:54+01:00", "duration_ms": 0, "result_preview": "null"}, {"type": "rotina", "label": "rotina", "status": "success", "node_id": "3", "timestamp": "2026-03-19T17:36:54+01:00", "duration_ms": 0, "result_preview": "null"}]', NULL, 1);
INSERT INTO public.tb_pipeline_execucoes VALUES (11, 12, 'success', '2026-03-19 13:36:55.089671-03', '2026-03-19 13:36:55.092359-03', 0, 4, 4, 4, 0, '{"1": null, "2": null, "3": null, "4": null}', '[{"type": "rotina", "label": "rotina", "status": "success", "node_id": 1, "timestamp": "2026-03-19T17:36:55+01:00", "duration_ms": 0, "result_preview": "null"}, {"type": "rotina", "label": "rotina", "status": "success", "node_id": 2, "timestamp": "2026-03-19T17:36:55+01:00", "duration_ms": 0, "result_preview": "null"}, {"type": "rotina", "label": "rotina", "status": "success", "node_id": 3, "timestamp": "2026-03-19T17:36:55+01:00", "duration_ms": 0, "result_preview": "null"}, {"type": "rotina", "label": "rotina", "status": "success", "node_id": 4, "timestamp": "2026-03-19T17:36:55+01:00", "duration_ms": 0, "result_preview": "null"}]', NULL, 1);
INSERT INTO public.tb_pipeline_execucoes VALUES (12, 13, 'success', '2026-03-19 16:24:10.79644-03', '2026-03-19 16:24:10.895954-03', 96, 3, 3, 3, 0, '{"1": {"started": true, "timestamp": "2026-03-19T20:24:10+01:00"}, "2": {"rows": [{"id": 1, "name": "Administrador", "role": "admin", "uuid": "5f2337a6-ca73-4183-9a5a-bc6f2a204a7d", "email": "admin@dmcmovie.com", "phone": "81983656068", "status": "active", "created_at": "2026-01-17 15:54:17.666008", "deleted_at": null, "ip_address": null, "last_login": "2026-03-11 18:05:07.824836", "updated_at": "2026-03-11 18:05:07.824836", "reseller_id": null, "max_profiles": 5, "max_downloads": 25, "password_hash": "$2y$10$8huwyRIyMHJTHV6/cXUO7e34hYcwFPZ9zIuolI/7/L/K772JYmSVq", "email_verified": true, "remember_token": "1f643336448904b8347e6ad5019f37ac544b3a7da7570250fdc38dcf192ac848", "credits_balance": 0, "two_factor_secret": null, "two_factor_enabled": false, "remember_token_expires": "2026-02-20 11:47:40", "total_credits_purchased": 0}], "total": 1, "truncated": false}, "3": {"finished": true}}', '[{"type": "trigger", "label": "Trigger", "status": "success", "node_id": 1, "timestamp": "2026-03-19T20:24:10+01:00", "duration_ms": 0, "result_preview": "2 itens"}, {"type": "sql_query", "label": "SQL Query", "status": "success", "node_id": "2", "timestamp": "2026-03-19T20:24:10+01:00", "duration_ms": 96, "result_preview": "1 registros"}, {"type": "end", "label": "End", "status": "success", "node_id": "3", "timestamp": "2026-03-19T20:24:10+01:00", "duration_ms": 0, "result_preview": "1 itens"}]', NULL, 1);
INSERT INTO public.tb_pipeline_execucoes VALUES (13, 13, 'success', '2026-03-19 16:27:05.325498-03', '2026-03-19 16:27:05.407105-03', 79, 3, 3, 3, 0, '{"1": {"started": true, "timestamp": "2026-03-19T20:27:05+01:00"}, "2": {"rows": [{"id": 1, "name": "Administrador", "role": "admin", "uuid": "5f2337a6-ca73-4183-9a5a-bc6f2a204a7d", "email": "admin@dmcmovie.com", "phone": "81983656068", "status": "active", "created_at": "2026-01-17 15:54:17.666008", "deleted_at": null, "ip_address": null, "last_login": "2026-03-11 18:05:07.824836", "updated_at": "2026-03-11 18:05:07.824836", "reseller_id": null, "max_profiles": 5, "max_downloads": 25, "password_hash": "$2y$10$8huwyRIyMHJTHV6/cXUO7e34hYcwFPZ9zIuolI/7/L/K772JYmSVq", "email_verified": true, "remember_token": "1f643336448904b8347e6ad5019f37ac544b3a7da7570250fdc38dcf192ac848", "credits_balance": 0, "two_factor_secret": null, "two_factor_enabled": false, "remember_token_expires": "2026-02-20 11:47:40", "total_credits_purchased": 0}], "total": 1, "truncated": false}, "3": {"finished": true}}', '[{"type": "trigger", "label": "Trigger", "status": "success", "node_id": 1, "timestamp": "2026-03-19T20:27:05+01:00", "duration_ms": 0, "result_preview": "2 itens"}, {"type": "sql_query", "label": "SQL Query", "status": "success", "node_id": "2", "timestamp": "2026-03-19T20:27:05+01:00", "duration_ms": 79, "result_preview": "1 registros"}, {"type": "end", "label": "End", "status": "success", "node_id": "3", "timestamp": "2026-03-19T20:27:05+01:00", "duration_ms": 0, "result_preview": "1 itens"}]', NULL, 1);
INSERT INTO public.tb_pipeline_execucoes VALUES (14, 28, 'success', '2026-03-20 12:59:08.850832-03', '2026-03-20 12:59:08.94271-03', 89, 3, 3, 3, 0, '{"1": {"started": true, "timestamp": "2026-03-20T16:59:08+01:00"}, "2": {"finished": true}, "3": {"rows": [{"id": 1, "role": "admin", "uuid": "32ac5ce9-c3db-4873-af65-72a023852e91", "email": "admin@dmcmovie.com", "status": "active", "created_at": "2026-01-17 15:49:04.028", "deleted_at": null, "ip_address": null, "last_login": null, "updated_at": "2026-01-17 15:49:04.028", "reseller_id": null, "password_hash": "$2y$12$LQv3c1yqBWVHxkd0LHAkCOYz6TtxMQJqhN8/LewY5NANs1jRp1G6u", "email_verified": true, "credits_balance": 0, "two_factor_secret": null, "two_factor_enabled": false, "total_credits_purchased": 0}], "total": 1, "truncated": false}}', '[{"type": "trigger", "label": "Trigger", "status": "success", "node_id": 1, "timestamp": "2026-03-20T16:59:08+01:00", "duration_ms": 0, "result_preview": "2 itens"}, {"type": "sql_query", "label": "SQL Query", "status": "success", "node_id": "3", "timestamp": "2026-03-20T16:59:08+01:00", "duration_ms": 89, "result_preview": "1 registros"}, {"type": "end", "label": "End", "status": "success", "node_id": "2", "timestamp": "2026-03-20T16:59:08+01:00", "duration_ms": 0, "result_preview": "1 itens"}]', NULL, 1);
INSERT INTO public.tb_pipeline_execucoes VALUES (15, 2, 'success', '2026-03-20 09:00:01-03', '2026-03-20 09:00:05-03', 4200, 0, 0, 0, 0, '{}', '[]', NULL, NULL);
INSERT INTO public.tb_pipeline_execucoes VALUES (16, 5, 'success', '2026-03-20 09:15:01-03', '2026-03-20 09:15:04-03', 3100, 0, 0, 0, 0, '{}', '[]', NULL, NULL);
INSERT INTO public.tb_pipeline_execucoes VALUES (17, 2, 'success', '2026-03-19 09:00:01-03', '2026-03-19 09:00:04-03', 3800, 0, 0, 0, 0, '{}', '[]', NULL, NULL);
INSERT INTO public.tb_pipeline_execucoes VALUES (18, 5, 'failed', '2026-03-18 09:15:01-03', '2026-03-18 09:15:02-03', 700, 0, 0, 0, 0, '{}', '[]', NULL, NULL);
INSERT INTO public.tb_pipeline_execucoes VALUES (19, 11, 'success', '2026-03-19 03:00:01-03', '2026-03-19 03:00:06-03', 5100, 0, 0, 0, 0, '{}', '[]', NULL, NULL);


--
-- TOC entry 5473 (class 0 OID 46258)
-- Dependencies: 261
-- Data for Name: tb_pipelines; Type: TABLE DATA; Schema: public; Owner: postgres
--

INSERT INTO public.tb_pipelines VALUES (1, 'TESTE_Manual_Trigger', 'Teste trigger manual - APAGAR', 'visual', false, '{"drawflow": {"Home": {"data": {"1": {"id": 1, "data": {"type": "trigger", "label": "Trigger", "trigger_type": "manual"}, "html": "", "name": "trigger", "class": "trigger", "pos_x": 350, "pos_y": 200, "inputs": {}, "outputs": {"output_1": {"connections": []}}, "typenode": "vue"}}}}}', '', '{}', '', 'manual', '{}', 1, '[]', 1, '2026-03-18 11:09:31.941917-03', '2026-03-18 11:09:31.941917-03');
INSERT INTO public.tb_pipelines VALUES (3, 'TESTE_ApiEvent_Trigger', 'Teste evento API - APAGAR', 'visual', false, '{"drawflow": {"Home": {"data": {"1": {"id": 1, "data": {"type": "trigger", "label": "Trigger", "trigger_type": "api_event", "evento_api_id": "1"}, "html": "", "name": "trigger", "class": "trigger", "pos_x": 350, "pos_y": 200, "inputs": {}, "outputs": {"output_1": {"connections": []}}, "typenode": "vue"}}}}}', '', '{}', '', 'api_event', '{}', 1, '[]', 1, '2026-03-18 11:09:58.927276-03', '2026-03-18 11:09:58.927276-03');
INSERT INTO public.tb_pipelines VALUES (4, 'TESTE_Webhook_Trigger', 'Teste webhook - APAGAR', 'visual', false, '{"drawflow": {"Home": {"data": {"1": {"id": 1, "data": {"type": "trigger", "label": "Trigger", "trigger_type": "webhook"}, "html": "", "name": "trigger", "class": "trigger", "pos_x": 350, "pos_y": 200, "inputs": {}, "outputs": {"output_1": {"connections": []}}, "typenode": "vue"}}}}}', '', '{}', '', 'webhook', '{}', 1, '[]', 1, '2026-03-18 11:09:59.273444-03', '2026-03-18 11:09:59.273444-03');
INSERT INTO public.tb_pipelines VALUES (6, 'PG_Pipeline_01_Chamados_Status', 'Pipeline sequencial busca chamados e agrupa por status', 'nocode', true, '{"drawflow": {"Home": {"data": {"1": {"id": 1, "data": {"nome": "PG_Teste_01_Chamados_Recentes", "id_rotina": "32"}, "html": "PG_Teste_01", "name": "rotina", "class": "rotina", "pos_x": 100, "pos_y": 200, "inputs": {"input_1": {"connections": []}}, "outputs": {"output_1": {"connections": [{"node": "2", "output": "input_1"}]}}, "typenode": "vue"}, "2": {"id": 2, "data": {"nome": "PG_Teste_02_Status_Chamados", "id_rotina": "33"}, "html": "PG_Teste_02", "name": "rotina", "class": "rotina", "pos_x": 400, "pos_y": 200, "inputs": {"input_1": {"connections": [{"node": "1", "input": "output_1"}]}}, "outputs": {"output_1": {"connections": []}}, "typenode": "vue"}}}}}', '', '{}', '', 'manual', '{}', 1, '["teste", "postgresql"]', 1, '2026-03-19 13:33:17.089515-03', '2026-03-19 13:33:17.089515-03');
INSERT INTO public.tb_pipelines VALUES (2, 'TESTE_Cron_Trigger', 'Teste agendamento cron - APAGAR', 'visual', true, '{"drawflow": {"Home": {"data": {}}}}', '', '{}', '0 9 * * *', 'cron', '{"trigger_type": "manual"}', 3, '[]', 1, '2026-03-18 11:09:58.505078-03', '2026-03-18 14:47:58.504807-03');
INSERT INTO public.tb_pipelines VALUES (5, 'PG_Pipeline_01_Chamados_Status', 'Pipeline sequencial: busca chamados e depois agrupa por status', 'nocode', true, '{"drawflow": {"Home": {"data": {"1": {"id": 1, "data": {"nome": "PG_Teste_01_Chamados_Recentes", "id_rotina": "32"}, "html": "PG_Teste_01", "name": "rotina", "class": "rotina", "pos_x": 100, "pos_y": 200, "inputs": {"input_1": {"connections": []}}, "outputs": {"output_1": {"connections": [{"node": "2", "output": "input_1"}]}}, "typenode": "vue"}, "2": {"id": 2, "data": {"nome": "PG_Teste_02_Status_Chamados", "id_rotina": "33"}, "html": "PG_Teste_02", "name": "rotina", "class": "rotina", "pos_x": 400, "pos_y": 200, "inputs": {"input_1": {"connections": [{"node": "1", "input": "output_1"}]}}, "outputs": {"output_1": {"connections": []}}, "typenode": "vue"}}}}}', '', '{}', '*/15 * * * *', 'cron', '{}', 1, '["teste", "postgresql", "chamados"]', 1, '2026-03-19 13:33:15.043714-03', '2026-03-19 13:33:15.043714-03');
INSERT INTO public.tb_pipelines VALUES (7, 'PG_Pipeline_02_Config_Audit', 'Pipeline com cron: config setores e audit', 'nocode', true, '{"drawflow": {"Home": {"data": {"1": {"id": 1, "data": {"nome": "PG_Teste_03_Configuracoes", "id_rotina": "34"}, "html": "Configuracoes", "name": "rotina", "class": "rotina", "pos_x": 100, "pos_y": 200, "inputs": {"input_1": {"connections": []}}, "outputs": {"output_1": {"connections": [{"node": "2", "output": "input_1"}]}}, "typenode": "vue"}, "2": {"id": 2, "data": {"nome": "PG_Teste_04_Setores", "id_rotina": "37"}, "html": "Setores", "name": "rotina", "class": "rotina", "pos_x": 400, "pos_y": 200, "inputs": {"input_1": {"connections": [{"node": "1", "input": "output_1"}]}}, "outputs": {"output_1": {"connections": [{"node": "3", "output": "input_1"}]}}, "typenode": "vue"}, "3": {"id": 3, "data": {"nome": "PG_Teste_05_Audit", "id_rotina": "38"}, "html": "Audit", "name": "rotina", "class": "rotina", "pos_x": 700, "pos_y": 200, "inputs": {"input_1": {"connections": [{"node": "2", "input": "output_1"}]}}, "outputs": {"output_1": {"connections": []}}, "typenode": "vue"}}}}}', '', '{}', '0 */6 * * *', 'cron', '{}', 1, '["teste", "postgresql", "agendado"]', 1, '2026-03-19 13:33:24.657117-03', '2026-03-19 13:33:24.657117-03');
INSERT INTO public.tb_pipelines VALUES (8, 'MY_Pipeline_03_Moradores_Avisos', 'Pipeline MySQL: moradores e avisos do condominio', 'nocode', true, '{"drawflow": {"Home": {"data": {"1": {"id": 1, "data": {"nome": "MY_Teste_01_Moradores", "id_rotina": "39"}, "html": "Moradores", "name": "rotina", "class": "rotina", "pos_x": 100, "pos_y": 200, "inputs": {"input_1": {"connections": []}}, "outputs": {"output_1": {"connections": [{"node": "2", "output": "input_1"}]}}, "typenode": "vue"}, "2": {"id": 2, "data": {"nome": "MY_Teste_02_Avisos", "id_rotina": "40"}, "html": "Avisos", "name": "rotina", "class": "rotina", "pos_x": 400, "pos_y": 200, "inputs": {"input_1": {"connections": [{"node": "1", "input": "output_1"}]}}, "outputs": {"output_1": {"connections": []}}, "typenode": "vue"}}}}}', '', '{}', '', 'manual', '{}', 1, '["teste", "mysql", "condominio"]', 1, '2026-03-19 13:33:30.045359-03', '2026-03-19 13:33:30.045359-03');
INSERT INTO public.tb_pipelines VALUES (9, 'MY_Pipeline_04_Areas_Anuncios_Roles', 'Pipeline MySQL agendado: areas anuncios e roles', 'nocode', true, '{"drawflow": {"Home": {"data": {"1": {"id": 1, "data": {"nome": "MY_Teste_03_Areas_Comuns", "id_rotina": "44"}, "html": "Areas", "name": "rotina", "class": "rotina", "pos_x": 100, "pos_y": 200, "inputs": {"input_1": {"connections": []}}, "outputs": {"output_1": {"connections": [{"node": "2", "output": "input_1"}]}}, "typenode": "vue"}, "2": {"id": 2, "data": {"nome": "MY_Teste_04_Anuncios", "id_rotina": "45"}, "html": "Anuncios", "name": "rotina", "class": "rotina", "pos_x": 400, "pos_y": 200, "inputs": {"input_1": {"connections": [{"node": "1", "input": "output_1"}]}}, "outputs": {"output_1": {"connections": [{"node": "3", "output": "input_1"}]}}, "typenode": "vue"}, "3": {"id": 3, "data": {"nome": "MY_Teste_05_Roles", "id_rotina": "46"}, "html": "Roles", "name": "rotina", "class": "rotina", "pos_x": 700, "pos_y": 200, "inputs": {"input_1": {"connections": [{"node": "2", "input": "output_1"}]}}, "outputs": {"output_1": {"connections": []}}, "typenode": "vue"}}}}}', '', '{}', '30 8 * * 1-5', 'cron', '{}', 1, '["teste", "mysql", "agendado"]', 1, '2026-03-19 13:33:36.003714-03', '2026-03-19 13:33:36.003714-03');
INSERT INTO public.tb_pipelines VALUES (10, 'MY_Pipeline_04_Areas_Anuncios_Roles', 'Pipeline MySQL agendado: areas anuncios e roles', 'nocode', true, '{"drawflow": {"Home": {"data": {"1": {"id": 1, "data": {"nome": "MY_Teste_03_Areas_Comuns", "id_rotina": "44"}, "html": "Areas", "name": "rotina", "class": "rotina", "pos_x": 100, "pos_y": 200, "inputs": {"input_1": {"connections": []}}, "outputs": {"output_1": {"connections": [{"node": "2", "output": "input_1"}]}}, "typenode": "vue"}, "2": {"id": 2, "data": {"nome": "MY_Teste_04_Anuncios", "id_rotina": "45"}, "html": "Anuncios", "name": "rotina", "class": "rotina", "pos_x": 400, "pos_y": 200, "inputs": {"input_1": {"connections": [{"node": "1", "input": "output_1"}]}}, "outputs": {"output_1": {"connections": [{"node": "3", "output": "input_1"}]}}, "typenode": "vue"}, "3": {"id": 3, "data": {"nome": "MY_Teste_05_Roles", "id_rotina": "46"}, "html": "Roles", "name": "rotina", "class": "rotina", "pos_x": 700, "pos_y": 200, "inputs": {"input_1": {"connections": [{"node": "2", "input": "output_1"}]}}, "outputs": {"output_1": {"connections": []}}, "typenode": "vue"}}}}}', '', '{}', '30 8 * * 1-5', 'cron', '{}', 1, '["teste", "mysql", "agendado"]', 1, '2026-03-19 13:33:38.54537-03', '2026-03-19 13:33:38.54537-03');
INSERT INTO public.tb_pipelines VALUES (12, 'Mixed_Pipeline_05_PG_MY_Paralelo', 'Pipeline misto PostgreSQL e MySQL em paralelo', 'nocode', true, '{"drawflow": {"Home": {"data": {"1": {"id": 1, "data": {"nome": "PG_Teste_01_Chamados", "id_rotina": "32"}, "html": "PG_Chamados", "name": "rotina", "class": "rotina", "pos_x": 100, "pos_y": 100, "inputs": {"input_1": {"connections": []}}, "outputs": {"output_1": {"connections": []}}, "typenode": "vue"}, "2": {"id": 2, "data": {"nome": "MY_Teste_01_Moradores", "id_rotina": "39"}, "html": "MY_Moradores", "name": "rotina", "class": "rotina", "pos_x": 100, "pos_y": 400, "inputs": {"input_1": {"connections": []}}, "outputs": {"output_1": {"connections": []}}, "typenode": "vue"}, "3": {"id": 3, "data": {"nome": "PG_Teste_05_Audit", "id_rotina": "38"}, "html": "PG_Audit", "name": "rotina", "class": "rotina", "pos_x": 400, "pos_y": 100, "inputs": {"input_1": {"connections": []}}, "outputs": {"output_1": {"connections": []}}, "typenode": "vue"}, "4": {"id": 4, "data": {"nome": "MY_Teste_05_Roles", "id_rotina": "46"}, "html": "MY_Roles", "name": "rotina", "class": "rotina", "pos_x": 400, "pos_y": 400, "inputs": {"input_1": {"connections": []}}, "outputs": {"output_1": {"connections": []}}, "typenode": "vue"}}}}}', '', '{}', '', 'manual', '{}', 1, '["teste", "misto", "paralelo"]', 1, '2026-03-19 13:33:47.101635-03', '2026-03-19 13:33:47.101635-03');
INSERT INTO public.tb_pipelines VALUES (13, 'Novo Pipeline', '', 'nocode', false, '{"drawflow": {"Home": {"data": {"1": {"id": 1, "data": {"type": "trigger", "label": "Trigger"}, "html": "<div class=\"df-node-container\" data-type=\"trigger\"><div class=\"df-node-header\" style=\"background: #10b98111;\"><div class=\"df-node-icon\" style=\"background:#10b981\"><i class=\"bi bi-play-circle\"></i></div><div><div class=\"df-node-title\">Trigger</div><div class=\"df-node-subtitle\">Manual</div></div></div></div>", "name": "trigger", "class": "trigger", "pos_x": 43, "pos_y": 40, "inputs": [], "outputs": {"output_1": {"connections": [{"node": "2", "output": "input_1"}]}}, "typenode": false}, "2": {"id": 2, "data": {"type": "sql_query", "label": "SQL Query", "sql_query": "select * from public.users;", "connection_id": "11", "connection_name": "DMC-Movie"}, "html": "<div class=\"df-node-container\" data-type=\"sql_query\"><div class=\"df-node-header\" style=\"background: #3b82f611;\"><div class=\"df-node-icon\" style=\"background:#3b82f6\"><i class=\"bi bi-database\"></i></div><div><div class=\"df-node-title\">SQL Query</div><div class=\"df-node-subtitle\">Selecione conexão</div></div></div><div class=\"df-node-body\"><span class=\"text-muted\">SQL não configurado</span></div></div>", "name": "sql_query", "class": "sql_query", "pos_x": 183, "pos_y": 227, "inputs": {"input_1": {"connections": [{"node": "1", "input": "output_1"}]}}, "outputs": {"output_1": {"connections": [{"node": "3", "output": "input_1"}]}}, "typenode": false}, "3": {"id": 3, "data": {"type": "end", "label": "End"}, "html": "<div class=\"df-node-container\" data-type=\"end\"><div class=\"df-node-header\" style=\"background: #dc262611;\"><div class=\"df-node-icon\" style=\"background:#dc2626\"><i class=\"bi bi-stop-circle\"></i></div><div><div class=\"df-node-title\">End</div></div></div></div>", "name": "end", "class": "end", "pos_x": 216, "pos_y": 493.8125, "inputs": {"input_1": {"connections": [{"node": "2", "input": "output_1"}]}}, "outputs": [], "typenode": false}}}}}', '', '{}', '', 'manual', '{"trigger_type": "manual"}', 2, '[]', 1, '2026-03-19 16:24:09.84646-03', '2026-03-19 16:27:04.326916-03');
INSERT INTO public.tb_pipelines VALUES (28, 'Novo Pipeline', '', 'nocode', false, '{"drawflow": {"Home": {"data": {"1": {"id": 1, "data": {"type": "trigger", "label": "Trigger"}, "html": "<div class=\"df-node-container\" data-type=\"trigger\"><div class=\"df-node-header\" style=\"background: #10b98111;\"><div class=\"df-node-icon\" style=\"background:#10b981\"><i class=\"bi bi-play-circle\"></i></div><div><div class=\"df-node-title\">Trigger</div><div class=\"df-node-subtitle\">Manual</div></div></div></div>", "name": "trigger", "class": "trigger", "pos_x": 267, "pos_y": 121.8125, "inputs": {}, "outputs": {"output_1": {"connections": [{"node": "3", "output": "input_1"}]}}, "typenode": false}, "2": {"id": 2, "data": {"type": "end", "label": "End"}, "html": "<div class=\"df-node-container\" data-type=\"end\"><div class=\"df-node-header\" style=\"background: #dc262611;\"><div class=\"df-node-icon\" style=\"background:#dc2626\"><i class=\"bi bi-stop-circle\"></i></div><div><div class=\"df-node-title\">End</div></div></div></div>", "name": "end", "class": "end", "pos_x": 565, "pos_y": 542.8125, "inputs": {"input_1": {"connections": [{"node": "3", "input": "output_1"}]}}, "outputs": {}, "typenode": false}, "3": {"id": 3, "data": {"type": "sql_query", "label": "SQL Query", "sql_query": "select * from public.users;", "connection_id": "11", "connection_name": "DMC-Movie"}, "html": "<div class=\"df-node-container\" data-type=\"sql_query\"><div class=\"df-node-header\" style=\"background: #3b82f611;\"><div class=\"df-node-icon\" style=\"background:#3b82f6\"><i class=\"bi bi-database\"></i></div><div><div class=\"df-node-title\">SQL Query</div><div class=\"df-node-subtitle\">Selecione conexão</div></div></div><div class=\"df-node-body\"><span class=\"text-muted\">SQL não configurado</span></div></div>", "name": "sql_query", "class": "sql_query", "pos_x": 404, "pos_y": 341.8125, "inputs": {"input_1": {"connections": [{"node": "1", "input": "output_1"}]}}, "outputs": {"output_1": {"connections": [{"node": "2", "output": "input_1"}]}}, "typenode": false}}}}}', '', '{}', '', 'manual', '{"trigger_type": "manual"}', 1, '[]', 1, '2026-03-20 12:59:08.233912-03', '2026-03-20 12:59:08.233912-03');
INSERT INTO public.tb_pipelines VALUES (11, 'Mixed_Pipeline_05_PG_MY_Paralelo', 'Pipeline misto PostgreSQL e MySQL em paralelo', 'nocode', true, '{"drawflow": {"Home": {"data": {"1": {"id": 1, "data": {"nome": "PG_Teste_01_Chamados", "id_rotina": "32"}, "html": "PG_Chamados", "name": "rotina", "class": "rotina", "pos_x": 100, "pos_y": 100, "inputs": {"input_1": {"connections": []}}, "outputs": {"output_1": {"connections": []}}, "typenode": "vue"}, "2": {"id": 2, "data": {"nome": "MY_Teste_01_Moradores", "id_rotina": "39"}, "html": "MY_Moradores", "name": "rotina", "class": "rotina", "pos_x": 100, "pos_y": 400, "inputs": {"input_1": {"connections": []}}, "outputs": {"output_1": {"connections": []}}, "typenode": "vue"}, "3": {"id": 3, "data": {"nome": "PG_Teste_05_Audit", "id_rotina": "38"}, "html": "PG_Audit", "name": "rotina", "class": "rotina", "pos_x": 400, "pos_y": 100, "inputs": {"input_1": {"connections": []}}, "outputs": {"output_1": {"connections": []}}, "typenode": "vue"}, "4": {"id": 4, "data": {"nome": "MY_Teste_05_Roles", "id_rotina": "46"}, "html": "MY_Roles", "name": "rotina", "class": "rotina", "pos_x": 400, "pos_y": 400, "inputs": {"input_1": {"connections": []}}, "outputs": {"output_1": {"connections": []}}, "typenode": "vue"}}}}}', '', '{}', '0 3 * * *', 'cron', '{}', 1, '["teste", "misto", "paralelo"]', 1, '2026-03-19 13:33:43.656845-03', '2026-03-19 13:33:43.656845-03');


--
-- TOC entry 5483 (class 0 OID 46365)
-- Dependencies: 271
-- Data for Name: tb_projetos; Type: TABLE DATA; Schema: public; Owner: postgres
--

INSERT INTO public.tb_projetos VALUES (1, 'SUTIN', '', 1, true, 1, '2026-03-19 19:25:59.714689-03', '2026-03-19 19:25:59.714689-03');
INSERT INTO public.tb_projetos VALUES (119, 'Infraestrutura TI', NULL, 65, true, NULL, '2026-03-20 14:06:40.067195-03', '2026-03-20 14:06:40.067195-03');
INSERT INTO public.tb_projetos VALUES (120, 'Sistema de Saúde', NULL, 66, true, NULL, '2026-03-20 14:06:40.069786-03', '2026-03-20 14:06:40.069786-03');
INSERT INTO public.tb_projetos VALUES (121, 'Portal Interno', NULL, 1, true, NULL, '2026-03-20 14:06:40.071243-03', '2026-03-20 14:06:40.071243-03');
INSERT INTO public.tb_projetos VALUES (122, 'SUTIN - HOMOLOGAÇÃO', '', 1, true, 1, '2026-03-20 14:30:44.907474-03', '2026-03-20 14:30:44.907474-03');
INSERT INTO public.tb_projetos VALUES (123, 'SUTIN - PRODUÇÃO', '', 1, true, 1, '2026-03-20 14:30:55.55537-03', '2026-03-20 14:30:55.55537-03');
INSERT INTO public.tb_projetos VALUES (124, 'SUTIN - TREINAMENTO', '', 1, true, 1, '2026-03-20 14:31:04.697582-03', '2026-03-20 14:31:04.697582-03');
INSERT INTO public.tb_projetos VALUES (70, 'TESTE', '', 40, true, 1, '2026-03-20 11:41:46.185871-03', '2026-03-20 11:41:46.185871-03');


--
-- TOC entry 5479 (class 0 OID 46324)
-- Dependencies: 267
-- Data for Name: tb_rate_limits; Type: TABLE DATA; Schema: public; Owner: postgres
--



--
-- TOC entry 5489 (class 0 OID 46435)
-- Dependencies: 277
-- Data for Name: tb_recurso_empresas; Type: TABLE DATA; Schema: public; Owner: postgres
--

INSERT INTO public.tb_recurso_empresas VALUES (11, 'conexao', 11, 40, '2026-03-20 14:06:58.146703-03');
INSERT INTO public.tb_recurso_empresas VALUES (12, 'conexao', 12, 1, '2026-03-20 14:06:58.152331-03');
INSERT INTO public.tb_recurso_empresas VALUES (13, 'conexao', 14, 1, '2026-03-20 14:06:58.15538-03');
INSERT INTO public.tb_recurso_empresas VALUES (16, 'rotina', 25, 40, '2026-03-20 14:06:58.171614-03');
INSERT INTO public.tb_recurso_empresas VALUES (17, 'rotina', 31, 40, '2026-03-20 14:06:58.176015-03');
INSERT INTO public.tb_recurso_empresas VALUES (18, 'rotina', 32, 40, '2026-03-20 14:06:58.17894-03');
INSERT INTO public.tb_recurso_empresas VALUES (21, 'rotina', 35, 40, '2026-03-20 14:06:58.188604-03');
INSERT INTO public.tb_recurso_empresas VALUES (22, 'rotina', 36, 40, '2026-03-20 14:06:58.201541-03');
INSERT INTO public.tb_recurso_empresas VALUES (23, 'rotina', 37, 40, '2026-03-20 14:06:58.203778-03');
INSERT INTO public.tb_recurso_empresas VALUES (24, 'rotina', 38, 40, '2026-03-20 14:06:58.205424-03');
INSERT INTO public.tb_recurso_empresas VALUES (25, 'rotina', 39, 40, '2026-03-20 14:06:58.212409-03');
INSERT INTO public.tb_recurso_empresas VALUES (26, 'rotina', 40, 40, '2026-03-20 14:06:58.215818-03');
INSERT INTO public.tb_recurso_empresas VALUES (27, 'rotina', 41, 40, '2026-03-20 14:06:58.22172-03');
INSERT INTO public.tb_recurso_empresas VALUES (28, 'rotina', 42, 40, '2026-03-20 14:06:58.228579-03');
INSERT INTO public.tb_recurso_empresas VALUES (29, 'rotina', 43, 40, '2026-03-20 14:06:58.231383-03');
INSERT INTO public.tb_recurso_empresas VALUES (30, 'rotina', 44, 40, '2026-03-20 14:06:58.244519-03');
INSERT INTO public.tb_recurso_empresas VALUES (31, 'rotina', 45, 40, '2026-03-20 14:06:58.246623-03');
INSERT INTO public.tb_recurso_empresas VALUES (32, 'rotina', 46, 40, '2026-03-20 14:06:58.257718-03');
INSERT INTO public.tb_recurso_empresas VALUES (33, 'rotina', 49, 1, '2026-03-20 14:09:16.669824-03');
INSERT INTO public.tb_recurso_empresas VALUES (34, 'rotina', 50, 1, '2026-03-20 14:09:16.6739-03');
INSERT INTO public.tb_recurso_empresas VALUES (35, 'rotina', 51, 1, '2026-03-20 14:09:16.677071-03');
INSERT INTO public.tb_recurso_empresas VALUES (36, 'rotina', 52, 1, '2026-03-20 14:09:16.680103-03');
INSERT INTO public.tb_recurso_empresas VALUES (37, 'rotina', 53, 1, '2026-03-20 14:09:16.682224-03');
INSERT INTO public.tb_recurso_empresas VALUES (38, 'rotina', 54, 1, '2026-03-20 14:09:16.684064-03');
INSERT INTO public.tb_recurso_empresas VALUES (39, 'rotina', 55, 1, '2026-03-20 14:09:16.68599-03');
INSERT INTO public.tb_recurso_empresas VALUES (40, 'rotina', 56, 1, '2026-03-20 14:09:16.687783-03');
INSERT INTO public.tb_recurso_empresas VALUES (57, 'conexao', 91, 65, '2026-03-20 14:09:59.474022-03');
INSERT INTO public.tb_recurso_empresas VALUES (58, 'rotina', 57, 65, '2026-03-20 14:09:59.481322-03');
INSERT INTO public.tb_recurso_empresas VALUES (59, 'rotina', 58, 65, '2026-03-20 14:09:59.490215-03');
INSERT INTO public.tb_recurso_empresas VALUES (60, 'rotina', 59, 65, '2026-03-20 14:09:59.494694-03');
INSERT INTO public.tb_recurso_empresas VALUES (61, 'conexao', 92, 66, '2026-03-20 14:09:59.497912-03');
INSERT INTO public.tb_recurso_empresas VALUES (62, 'rotina', 60, 66, '2026-03-20 14:09:59.507492-03');
INSERT INTO public.tb_recurso_empresas VALUES (63, 'rotina', 61, 66, '2026-03-20 14:09:59.51233-03');
INSERT INTO public.tb_recurso_empresas VALUES (64, 'rotina', 62, 66, '2026-03-20 14:09:59.514217-03');
INSERT INTO public.tb_recurso_empresas VALUES (65, 'conexao', 93, 1, '2026-03-20 14:09:59.52133-03');
INSERT INTO public.tb_recurso_empresas VALUES (66, 'rotina', 63, 1, '2026-03-20 14:09:59.523624-03');
INSERT INTO public.tb_recurso_empresas VALUES (67, 'rotina', 64, 1, '2026-03-20 14:09:59.531722-03');
INSERT INTO public.tb_recurso_empresas VALUES (68, 'rotina', 65, 1, '2026-03-20 14:09:59.534549-03');
INSERT INTO public.tb_recurso_empresas VALUES (69, 'workflow', 1, 1, '2026-03-20 14:09:59.537989-03');
INSERT INTO public.tb_recurso_empresas VALUES (70, 'workflow', 2, 1, '2026-03-20 14:09:59.540413-03');
INSERT INTO public.tb_recurso_empresas VALUES (71, 'workflow', 3, 1, '2026-03-20 14:09:59.547287-03');
INSERT INTO public.tb_recurso_empresas VALUES (72, 'workflow', 4, 1, '2026-03-20 14:09:59.552232-03');
INSERT INTO public.tb_recurso_empresas VALUES (73, 'workflow', 5, 40, '2026-03-20 14:09:59.555375-03');
INSERT INTO public.tb_recurso_empresas VALUES (74, 'workflow', 6, 40, '2026-03-20 14:09:59.557759-03');
INSERT INTO public.tb_recurso_empresas VALUES (75, 'workflow', 7, 40, '2026-03-20 14:09:59.561592-03');
INSERT INTO public.tb_recurso_empresas VALUES (76, 'pipeline', 1, 1, '2026-03-20 14:09:59.566035-03');
INSERT INTO public.tb_recurso_empresas VALUES (77, 'pipeline', 2, 1, '2026-03-20 14:09:59.573049-03');
INSERT INTO public.tb_recurso_empresas VALUES (78, 'pipeline', 3, 1, '2026-03-20 14:09:59.575114-03');
INSERT INTO public.tb_recurso_empresas VALUES (79, 'pipeline', 4, 1, '2026-03-20 14:09:59.577397-03');
INSERT INTO public.tb_recurso_empresas VALUES (80, 'pipeline', 5, 1, '2026-03-20 14:09:59.588921-03');
INSERT INTO public.tb_recurso_empresas VALUES (81, 'pipeline', 6, 40, '2026-03-20 14:09:59.602959-03');
INSERT INTO public.tb_recurso_empresas VALUES (82, 'pipeline', 7, 40, '2026-03-20 14:09:59.616267-03');
INSERT INTO public.tb_recurso_empresas VALUES (83, 'pipeline', 8, 40, '2026-03-20 14:09:59.622749-03');
INSERT INTO public.tb_recurso_empresas VALUES (84, 'pipeline', 9, 40, '2026-03-20 14:09:59.62642-03');
INSERT INTO public.tb_recurso_empresas VALUES (85, 'pipeline', 10, 40, '2026-03-20 14:09:59.628583-03');
INSERT INTO public.tb_recurso_empresas VALUES (86, 'pipeline', 11, 65, '2026-03-20 14:09:59.630244-03');
INSERT INTO public.tb_recurso_empresas VALUES (87, 'pipeline', 12, 65, '2026-03-20 14:09:59.633724-03');
INSERT INTO public.tb_recurso_empresas VALUES (88, 'pipeline', 13, 65, '2026-03-20 14:09:59.635376-03');
INSERT INTO public.tb_recurso_empresas VALUES (89, 'pipeline', 28, 65, '2026-03-20 14:09:59.637711-03');
INSERT INTO public.tb_recurso_empresas VALUES (90, 'conexao', 16, 1, '2026-03-20 14:15:56.124666-03');
INSERT INTO public.tb_recurso_empresas VALUES (92, 'conexao', 15, 1, '2026-03-20 14:17:22.074806-03');
INSERT INTO public.tb_recurso_empresas VALUES (93, 'rotina', 33, 1, '2026-03-20 14:18:53.314086-03');
INSERT INTO public.tb_recurso_empresas VALUES (94, 'rotina', 34, 1, '2026-03-20 14:19:57.664247-03');


--
-- TOC entry 5491 (class 0 OID 46452)
-- Dependencies: 279
-- Data for Name: tb_recurso_projetos; Type: TABLE DATA; Schema: public; Owner: postgres
--

INSERT INTO public.tb_recurso_projetos VALUES (9, 'conexao', 11, 70, '2026-03-20 14:06:58.148678-03');
INSERT INTO public.tb_recurso_projetos VALUES (10, 'conexao', 12, 1, '2026-03-20 14:06:58.153156-03');
INSERT INTO public.tb_recurso_projetos VALUES (11, 'conexao', 14, 1, '2026-03-20 14:06:58.156388-03');
INSERT INTO public.tb_recurso_projetos VALUES (14, 'rotina', 25, 70, '2026-03-20 14:06:58.172355-03');
INSERT INTO public.tb_recurso_projetos VALUES (15, 'rotina', 31, 70, '2026-03-20 14:06:58.176705-03');
INSERT INTO public.tb_recurso_projetos VALUES (16, 'rotina', 32, 70, '2026-03-20 14:06:58.179632-03');
INSERT INTO public.tb_recurso_projetos VALUES (19, 'rotina', 35, 70, '2026-03-20 14:06:58.189227-03');
INSERT INTO public.tb_recurso_projetos VALUES (20, 'rotina', 36, 70, '2026-03-20 14:06:58.202218-03');
INSERT INTO public.tb_recurso_projetos VALUES (21, 'rotina', 37, 70, '2026-03-20 14:06:58.204415-03');
INSERT INTO public.tb_recurso_projetos VALUES (22, 'rotina', 38, 70, '2026-03-20 14:06:58.206062-03');
INSERT INTO public.tb_recurso_projetos VALUES (23, 'rotina', 39, 70, '2026-03-20 14:06:58.213015-03');
INSERT INTO public.tb_recurso_projetos VALUES (24, 'rotina', 40, 70, '2026-03-20 14:06:58.216421-03');
INSERT INTO public.tb_recurso_projetos VALUES (25, 'rotina', 41, 70, '2026-03-20 14:06:58.222354-03');
INSERT INTO public.tb_recurso_projetos VALUES (26, 'rotina', 42, 70, '2026-03-20 14:06:58.229201-03');
INSERT INTO public.tb_recurso_projetos VALUES (27, 'rotina', 43, 70, '2026-03-20 14:06:58.232014-03');
INSERT INTO public.tb_recurso_projetos VALUES (28, 'rotina', 44, 70, '2026-03-20 14:06:58.245178-03');
INSERT INTO public.tb_recurso_projetos VALUES (29, 'rotina', 45, 70, '2026-03-20 14:06:58.247237-03');
INSERT INTO public.tb_recurso_projetos VALUES (30, 'rotina', 46, 70, '2026-03-20 14:06:58.258393-03');
INSERT INTO public.tb_recurso_projetos VALUES (31, 'rotina', 49, 1, '2026-03-20 14:09:16.671296-03');
INSERT INTO public.tb_recurso_projetos VALUES (32, 'rotina', 50, 1, '2026-03-20 14:09:16.674928-03');
INSERT INTO public.tb_recurso_projetos VALUES (33, 'rotina', 51, 1, '2026-03-20 14:09:16.677958-03');
INSERT INTO public.tb_recurso_projetos VALUES (34, 'rotina', 52, 1, '2026-03-20 14:09:16.680783-03');
INSERT INTO public.tb_recurso_projetos VALUES (35, 'rotina', 53, 1, '2026-03-20 14:09:16.682719-03');
INSERT INTO public.tb_recurso_projetos VALUES (36, 'rotina', 54, 1, '2026-03-20 14:09:16.684597-03');
INSERT INTO public.tb_recurso_projetos VALUES (37, 'rotina', 55, 1, '2026-03-20 14:09:16.686541-03');
INSERT INTO public.tb_recurso_projetos VALUES (38, 'rotina', 56, 1, '2026-03-20 14:09:16.688237-03');
INSERT INTO public.tb_recurso_projetos VALUES (55, 'conexao', 91, 119, '2026-03-20 14:09:59.475374-03');
INSERT INTO public.tb_recurso_projetos VALUES (56, 'rotina', 57, 119, '2026-03-20 14:09:59.482033-03');
INSERT INTO public.tb_recurso_projetos VALUES (57, 'rotina', 58, 119, '2026-03-20 14:09:59.491185-03');
INSERT INTO public.tb_recurso_projetos VALUES (58, 'rotina', 59, 119, '2026-03-20 14:09:59.495302-03');
INSERT INTO public.tb_recurso_projetos VALUES (59, 'conexao', 92, 120, '2026-03-20 14:09:59.498518-03');
INSERT INTO public.tb_recurso_projetos VALUES (60, 'rotina', 60, 120, '2026-03-20 14:09:59.50865-03');
INSERT INTO public.tb_recurso_projetos VALUES (61, 'rotina', 61, 120, '2026-03-20 14:09:59.512801-03');
INSERT INTO public.tb_recurso_projetos VALUES (62, 'rotina', 62, 120, '2026-03-20 14:09:59.514673-03');
INSERT INTO public.tb_recurso_projetos VALUES (63, 'conexao', 93, 121, '2026-03-20 14:09:59.521958-03');
INSERT INTO public.tb_recurso_projetos VALUES (64, 'rotina', 63, 121, '2026-03-20 14:09:59.524132-03');
INSERT INTO public.tb_recurso_projetos VALUES (65, 'rotina', 64, 121, '2026-03-20 14:09:59.532602-03');
INSERT INTO public.tb_recurso_projetos VALUES (66, 'rotina', 65, 121, '2026-03-20 14:09:59.535073-03');
INSERT INTO public.tb_recurso_projetos VALUES (67, 'workflow', 1, 1, '2026-03-20 14:09:59.539024-03');
INSERT INTO public.tb_recurso_projetos VALUES (68, 'workflow', 2, 1, '2026-03-20 14:09:59.54104-03');
INSERT INTO public.tb_recurso_projetos VALUES (69, 'workflow', 3, 1, '2026-03-20 14:09:59.54801-03');
INSERT INTO public.tb_recurso_projetos VALUES (70, 'workflow', 4, 1, '2026-03-20 14:09:59.553379-03');
INSERT INTO public.tb_recurso_projetos VALUES (71, 'workflow', 5, 70, '2026-03-20 14:09:59.556066-03');
INSERT INTO public.tb_recurso_projetos VALUES (72, 'workflow', 6, 70, '2026-03-20 14:09:59.558865-03');
INSERT INTO public.tb_recurso_projetos VALUES (73, 'workflow', 7, 70, '2026-03-20 14:09:59.562293-03');
INSERT INTO public.tb_recurso_projetos VALUES (74, 'pipeline', 1, 1, '2026-03-20 14:09:59.566664-03');
INSERT INTO public.tb_recurso_projetos VALUES (75, 'pipeline', 2, 1, '2026-03-20 14:09:59.573978-03');
INSERT INTO public.tb_recurso_projetos VALUES (76, 'pipeline', 3, 1, '2026-03-20 14:09:59.575741-03');
INSERT INTO public.tb_recurso_projetos VALUES (77, 'pipeline', 4, 1, '2026-03-20 14:09:59.578031-03');
INSERT INTO public.tb_recurso_projetos VALUES (78, 'pipeline', 5, 1, '2026-03-20 14:09:59.59031-03');
INSERT INTO public.tb_recurso_projetos VALUES (79, 'pipeline', 6, 70, '2026-03-20 14:09:59.604566-03');
INSERT INTO public.tb_recurso_projetos VALUES (80, 'pipeline', 7, 70, '2026-03-20 14:09:59.617939-03');
INSERT INTO public.tb_recurso_projetos VALUES (81, 'pipeline', 8, 70, '2026-03-20 14:09:59.623444-03');
INSERT INTO public.tb_recurso_projetos VALUES (82, 'pipeline', 9, 70, '2026-03-20 14:09:59.627036-03');
INSERT INTO public.tb_recurso_projetos VALUES (83, 'pipeline', 10, 70, '2026-03-20 14:09:59.629195-03');
INSERT INTO public.tb_recurso_projetos VALUES (84, 'pipeline', 11, 119, '2026-03-20 14:09:59.630838-03');
INSERT INTO public.tb_recurso_projetos VALUES (85, 'pipeline', 12, 119, '2026-03-20 14:09:59.634332-03');
INSERT INTO public.tb_recurso_projetos VALUES (86, 'pipeline', 13, 119, '2026-03-20 14:09:59.636085-03');
INSERT INTO public.tb_recurso_projetos VALUES (87, 'pipeline', 28, 119, '2026-03-20 14:09:59.638388-03');
INSERT INTO public.tb_recurso_projetos VALUES (88, 'conexao', 16, 1, '2026-03-20 14:15:56.126786-03');
INSERT INTO public.tb_recurso_projetos VALUES (89, 'conexao', 15, 1, '2026-03-20 14:17:22.07691-03');
INSERT INTO public.tb_recurso_projetos VALUES (90, 'rotina', 33, 1, '2026-03-20 14:18:53.319875-03');
INSERT INTO public.tb_recurso_projetos VALUES (91, 'rotina', 34, 1, '2026-03-20 14:19:57.668223-03');
INSERT INTO public.tb_recurso_projetos VALUES (92, 'conexao', 94, 123, '2026-03-20 15:38:39.179797-03');


--
-- TOC entry 5443 (class 0 OID 45572)
-- Dependencies: 230
-- Data for Name: tb_rotinas; Type: TABLE DATA; Schema: public; Owner: postgres
--

INSERT INTO public.tb_rotinas OVERRIDING SYSTEM VALUE VALUES (34, 'PG_Teste_03_Configuracoes', 'Listar configuracoes do sistema Helpdesk', 15, 1, false, '2026-03-19 13:36:16.839667-03', NULL, NULL, '2026-03-19 13:20:12.43046-03', '0 8 * * *', true, NULL, 0, 3, '2026-03-20 08:00:05-03', NULL, NULL, NULL, false, 300, false);
INSERT INTO public.tb_rotinas OVERRIDING SYSTEM VALUE VALUES (39, 'MY_Teste_01_Moradores', 'Listar moradores do condominio', 16, 1, false, '2026-03-19 15:42:34.057815-03', NULL, NULL, '2026-03-19 13:20:26.41508-03', NULL, false, NULL, 0, 3, NULL, NULL, NULL, NULL, false, 300, true);
INSERT INTO public.tb_rotinas OVERRIDING SYSTEM VALUE VALUES (40, 'MY_Teste_02_Avisos', 'Listar avisos do condominio', 16, 1, false, '2026-03-19 15:42:34.278708-03', NULL, NULL, '2026-03-19 13:20:33.049761-03', NULL, false, NULL, 0, 3, NULL, NULL, NULL, NULL, false, 300, true);
INSERT INTO public.tb_rotinas OVERRIDING SYSTEM VALUE VALUES (41, 'MY_Teste_03_Areas_Comuns', 'Listar areas comuns do condominio', 16, 1, false, '2026-03-19 15:42:34.497068-03', NULL, NULL, '2026-03-19 13:20:36.814543-03', NULL, false, NULL, 0, 3, NULL, NULL, NULL, NULL, false, 300, true);
INSERT INTO public.tb_rotinas OVERRIDING SYSTEM VALUE VALUES (42, 'MY_Teste_04_Anuncios', 'Listar anuncios do mercado do condominio', 16, 1, false, '2026-03-19 15:42:34.779689-03', NULL, NULL, '2026-03-19 13:20:37.033718-03', NULL, false, NULL, 0, 3, NULL, NULL, NULL, NULL, false, 300, true);
INSERT INTO public.tb_rotinas OVERRIDING SYSTEM VALUE VALUES (43, 'MY_Teste_05_Roles_Permissions', 'Listar roles e permissions do sistema', 16, 1, false, '2026-03-19 15:42:34.9857-03', NULL, NULL, '2026-03-19 13:20:37.243474-03', NULL, false, NULL, 0, 3, NULL, NULL, NULL, NULL, false, 300, true);
INSERT INTO public.tb_rotinas OVERRIDING SYSTEM VALUE VALUES (44, 'MY_Teste_03_Areas_Comuns', 'Listar areas comuns do condominio', 16, 1, false, '2026-03-19 15:42:35.206282-03', NULL, NULL, '2026-03-19 13:20:39.373844-03', NULL, false, NULL, 0, 3, NULL, NULL, NULL, NULL, false, 300, true);
INSERT INTO public.tb_rotinas OVERRIDING SYSTEM VALUE VALUES (32, 'PG_Teste_01_Chamados_Recentes', 'SELECT nos chamados mais recentes do Helpdesk', 15, 1, false, '2026-03-19 13:36:34.436982-03', NULL, NULL, '2026-03-19 13:20:04.673532-03', NULL, true, NULL, 0, 3, NULL, NULL, NULL, NULL, false, 300, true);
INSERT INTO public.tb_rotinas OVERRIDING SYSTEM VALUE VALUES (46, 'MY_Teste_05_Roles_Permissions', 'Listar roles e permissions do sistema', 16, 1, false, '2026-03-19 15:41:42.527547-03', NULL, NULL, '2026-03-19 13:20:45.818907-03', NULL, true, NULL, 0, 3, NULL, NULL, NULL, NULL, false, 300, true);
INSERT INTO public.tb_rotinas OVERRIDING SYSTEM VALUE VALUES (37, 'PG_Teste_04_Setores_Categorias', 'Listar setores e categorias do Helpdesk', 15, 1, false, '2026-03-19 13:36:17.103812-03', NULL, NULL, '2026-03-19 13:20:19.879778-03', '0 9 * * *', true, NULL, 0, 3, NULL, NULL, NULL, NULL, false, 300, false);
INSERT INTO public.tb_rotinas OVERRIDING SYSTEM VALUE VALUES (25, 'DMC-Movie - rotina1', 'Consultas de rotina', 11, 1, false, NULL, NULL, NULL, '2026-02-03 08:44:15.712766-03', NULL, false, '2026-02-03 17:08:00-03', 0, 3, '2026-02-03 17:07:21.998825-03', NULL, NULL, NULL, false, 300, true);
INSERT INTO public.tb_rotinas OVERRIDING SYSTEM VALUE VALUES (38, 'PG_Teste_05_Audit_Log', 'Ultimos registros de auditoria do Helpdesk', 15, 1, false, '2026-03-19 13:36:17.362831-03', NULL, NULL, '2026-03-19 13:20:23.042136-03', NULL, true, NULL, 0, 3, NULL, NULL, NULL, NULL, false, 300, true);
INSERT INTO public.tb_rotinas OVERRIDING SYSTEM VALUE VALUES (45, 'MY_Teste_04_Anuncios', 'Listar anuncios do mercado do condominio', 16, 1, false, '2026-03-19 15:42:35.403524-03', NULL, NULL, '2026-03-19 13:20:43.256874-03', NULL, false, NULL, 0, 3, NULL, NULL, NULL, NULL, false, 300, true);
INSERT INTO public.tb_rotinas OVERRIDING SYSTEM VALUE VALUES (31, 'PG_Teste_01_Chamados_Recentes', 'SELECT nos chamados mais recentes do Helpdesk', 15, 1, false, NULL, NULL, NULL, '2026-03-19 13:20:01.50536-03', NULL, false, NULL, 0, 3, NULL, NULL, NULL, NULL, false, 300, true);
INSERT INTO public.tb_rotinas OVERRIDING SYSTEM VALUE VALUES (35, 'PG_Teste_04_Setores_Categorias', 'Listar setores e categorias do Helpdesk', 15, 1, false, NULL, NULL, NULL, '2026-03-19 13:20:17.220756-03', NULL, false, NULL, 0, 3, NULL, NULL, NULL, NULL, false, 300, true);
INSERT INTO public.tb_rotinas OVERRIDING SYSTEM VALUE VALUES (36, 'PG_Teste_05_Audit_Log', 'Ultimos registros de auditoria do Helpdesk', 15, 1, false, NULL, NULL, NULL, '2026-03-19 13:20:17.392416-03', NULL, false, NULL, 0, 3, NULL, NULL, NULL, NULL, false, 300, true);
INSERT INTO public.tb_rotinas OVERRIDING SYSTEM VALUE VALUES (51, 'SEC_ERGON_03_Lotacoes', 'Lotações por departamento', 12, 1, false, NULL, NULL, NULL, '2026-03-20 14:09:16.676022-03', NULL, false, NULL, 0, 3, NULL, NULL, NULL, NULL, false, 300, true);
INSERT INTO public.tb_rotinas OVERRIDING SYSTEM VALUE VALUES (52, 'SEC_ERGON_04_Folha_Pagamento', 'Dados de folha de pagamento', 12, 1, false, NULL, NULL, NULL, '2026-03-20 14:09:16.678987-03', NULL, false, NULL, 0, 3, NULL, NULL, NULL, NULL, false, 300, true);
INSERT INTO public.tb_rotinas OVERRIDING SYSTEM VALUE VALUES (53, 'SEC_ERGON_05_Ferias', 'Controle de férias', 12, 1, false, NULL, NULL, NULL, '2026-03-20 14:09:16.681542-03', NULL, false, NULL, 0, 3, NULL, NULL, NULL, NULL, false, 300, true);
INSERT INTO public.tb_rotinas OVERRIDING SYSTEM VALUE VALUES (55, 'SEC_EXAM_02_Exames', 'Exames realizados', 14, 1, false, NULL, NULL, NULL, '2026-03-20 14:09:16.68527-03', NULL, false, NULL, 0, 3, NULL, NULL, NULL, NULL, false, 300, true);
INSERT INTO public.tb_rotinas OVERRIDING SYSTEM VALUE VALUES (56, 'SEC_EXAM_03_Laudos', 'Laudos emitidos', 14, 1, false, NULL, NULL, NULL, '2026-03-20 14:09:16.687195-03', NULL, false, NULL, 0, 3, NULL, NULL, NULL, NULL, false, 300, true);
INSERT INTO public.tb_rotinas OVERRIDING SYSTEM VALUE VALUES (59, 'Rotina_PG_Infra_Servidores_03', 'Rotina de teste para PG_Infra_Servidores', 91, 1, false, NULL, NULL, NULL, '2026-03-20 14:09:59.492218-03', NULL, false, NULL, 0, 3, NULL, NULL, NULL, NULL, false, 300, true);
INSERT INTO public.tb_rotinas OVERRIDING SYSTEM VALUE VALUES (62, 'Rotina_MY_Saude_Prontuarios_03', 'Rotina de teste para MY_Saude_Prontuarios', 92, 1, false, NULL, NULL, NULL, '2026-03-20 14:09:59.51345-03', NULL, false, NULL, 0, 3, NULL, NULL, NULL, NULL, false, 300, true);
INSERT INTO public.tb_rotinas OVERRIDING SYSTEM VALUE VALUES (64, 'Rotina_PG_Portal_Interno_02', 'Rotina de teste para PG_Portal_Interno', 93, 1, false, NULL, NULL, NULL, '2026-03-20 14:09:59.524924-03', NULL, false, NULL, 0, 3, NULL, NULL, NULL, NULL, false, 300, true);
INSERT INTO public.tb_rotinas OVERRIDING SYSTEM VALUE VALUES (65, 'Rotina_PG_Portal_Interno_03', 'Rotina de teste para PG_Portal_Interno', 93, 1, false, NULL, NULL, NULL, '2026-03-20 14:09:59.533672-03', NULL, false, NULL, 0, 3, NULL, NULL, NULL, NULL, false, 300, true);
INSERT INTO public.tb_rotinas OVERRIDING SYSTEM VALUE VALUES (49, 'SEC_ERGON_01_Funcionarios', 'Lista de funcionarios do ERGON', 12, 1, false, NULL, NULL, NULL, '2026-03-20 14:09:16.66714-03', '0 7 * * 1-5', true, NULL, 0, 3, '2026-03-19 07:00:02-03', NULL, NULL, NULL, false, 300, true);
INSERT INTO public.tb_rotinas OVERRIDING SYSTEM VALUE VALUES (33, 'PG_Teste_02_Status_Chamados', 'Contagem de chamados por status', 15, 1, false, '2026-03-19 13:36:16.596409-03', NULL, NULL, '2026-03-19 13:20:07.728357-03', '*/30 * * * *', true, NULL, 0, 3, '2026-03-20 07:30:01-03', NULL, NULL, NULL, false, 300, false);
INSERT INTO public.tb_rotinas OVERRIDING SYSTEM VALUE VALUES (50, 'SEC_ERGON_02_Cargos', 'Cargos e salarios', 12, 1, false, NULL, NULL, NULL, '2026-03-20 14:09:16.672776-03', '0 12 * * 1-5', true, NULL, 0, 3, '2026-03-19 12:00:01-03', NULL, NULL, NULL, false, 300, true);
INSERT INTO public.tb_rotinas OVERRIDING SYSTEM VALUE VALUES (54, 'SEC_EXAM_01_Pacientes', 'Lista de pacientes DMC-Exam', 14, 1, false, NULL, NULL, NULL, '2026-03-20 14:09:16.683409-03', '0 6 * * *', true, NULL, 0, 3, '2026-03-18 06:00:03-03', NULL, NULL, NULL, false, 300, true);
INSERT INTO public.tb_rotinas OVERRIDING SYSTEM VALUE VALUES (63, 'Rotina_PG_Portal_Interno_01', 'Rotina de teste para PG_Portal_Interno', 93, 1, false, NULL, NULL, NULL, '2026-03-20 14:09:59.522627-03', '0 */4 * * *', true, NULL, 0, 3, '2026-03-18 08:00:01-03', NULL, NULL, NULL, false, 300, true);
INSERT INTO public.tb_rotinas OVERRIDING SYSTEM VALUE VALUES (57, 'Rotina_PG_Infra_Servidores_01', 'Rotina de teste para PG_Infra_Servidores', 91, 1, false, NULL, NULL, NULL, '2026-03-20 14:09:59.476469-03', '*/10 * * * *', true, NULL, 0, 3, '2026-03-20 06:10:01-03', NULL, NULL, NULL, false, 300, true);
INSERT INTO public.tb_rotinas OVERRIDING SYSTEM VALUE VALUES (58, 'Rotina_PG_Infra_Servidores_02', 'Rotina de teste para PG_Infra_Servidores', 91, 1, false, NULL, NULL, NULL, '2026-03-20 14:09:59.483153-03', '0 */2 * * *', true, NULL, 0, 3, '2026-03-19 10:00:02-03', NULL, NULL, NULL, false, 300, true);
INSERT INTO public.tb_rotinas OVERRIDING SYSTEM VALUE VALUES (60, 'Rotina_MY_Saude_Prontuarios_01', 'Rotina de teste para MY_Saude_Prontuarios', 92, 1, false, NULL, NULL, NULL, '2026-03-20 14:09:59.499538-03', '0 5 * * *', true, NULL, 0, 3, '2026-03-20 05:00:02-03', NULL, NULL, NULL, false, 300, true);
INSERT INTO public.tb_rotinas OVERRIDING SYSTEM VALUE VALUES (61, 'Rotina_MY_Saude_Prontuarios_02', 'Rotina de teste para MY_Saude_Prontuarios', 92, 1, false, NULL, NULL, NULL, '2026-03-20 14:09:59.509761-03', '0 8,14,20 * * *', true, NULL, 0, 3, '2026-03-20 08:00:01-03', NULL, NULL, NULL, false, 300, true);


--
-- TOC entry 5485 (class 0 OID 46391)
-- Dependencies: 273
-- Data for Name: tb_usuario_empresas; Type: TABLE DATA; Schema: public; Owner: postgres
--

INSERT INTO public.tb_usuario_empresas VALUES (126, 13, 1, '2026-03-20 17:50:58.858616-03');
INSERT INTO public.tb_usuario_empresas VALUES (2, 3, 1, '2026-03-19 20:05:53.034267-03');
INSERT INTO public.tb_usuario_empresas VALUES (108, 107, 65, '2026-03-20 14:06:58.113563-03');
INSERT INTO public.tb_usuario_empresas VALUES (109, 108, 66, '2026-03-20 14:06:58.121074-03');
INSERT INTO public.tb_usuario_empresas VALUES (110, 109, 1, '2026-03-20 14:06:58.12326-03');
INSERT INTO public.tb_usuario_empresas VALUES (111, 109, 65, '2026-03-20 14:06:58.123833-03');
INSERT INTO public.tb_usuario_empresas VALUES (112, 110, 40, '2026-03-20 14:06:58.135904-03');
INSERT INTO public.tb_usuario_empresas VALUES (124, 23, 1, '2026-03-20 16:02:44.468846-03');
INSERT INTO public.tb_usuario_empresas VALUES (125, 32, 1, '2026-03-20 16:08:23.246712-03');


--
-- TOC entry 5487 (class 0 OID 46413)
-- Dependencies: 275
-- Data for Name: tb_usuario_projetos; Type: TABLE DATA; Schema: public; Owner: postgres
--

INSERT INTO public.tb_usuario_projetos VALUES (2, 3, 1, '2026-03-19 20:05:53.034267-03');
INSERT INTO public.tb_usuario_projetos VALUES (205, 23, 123, '2026-03-20 16:02:44.468846-03');
INSERT INTO public.tb_usuario_projetos VALUES (206, 32, 122, '2026-03-20 16:08:23.246712-03');
INSERT INTO public.tb_usuario_projetos VALUES (207, 32, 123, '2026-03-20 16:08:23.246712-03');
INSERT INTO public.tb_usuario_projetos VALUES (208, 32, 124, '2026-03-20 16:08:23.246712-03');
INSERT INTO public.tb_usuario_projetos VALUES (209, 13, 123, '2026-03-20 17:50:58.858616-03');
INSERT INTO public.tb_usuario_projetos VALUES (178, 107, 119, '2026-03-20 14:06:58.114928-03');
INSERT INTO public.tb_usuario_projetos VALUES (179, 108, 120, '2026-03-20 14:06:58.121772-03');
INSERT INTO public.tb_usuario_projetos VALUES (180, 109, 1, '2026-03-20 14:06:58.12435-03');
INSERT INTO public.tb_usuario_projetos VALUES (181, 109, 121, '2026-03-20 14:06:58.124814-03');
INSERT INTO public.tb_usuario_projetos VALUES (182, 109, 119, '2026-03-20 14:06:58.125261-03');
INSERT INTO public.tb_usuario_projetos VALUES (183, 110, 70, '2026-03-20 14:06:58.137226-03');


--
-- TOC entry 5439 (class 0 OID 45550)
-- Dependencies: 226
-- Data for Name: tb_usuarios; Type: TABLE DATA; Schema: public; Owner: postgres
--

INSERT INTO public.tb_usuarios OVERRIDING SYSTEM VALUE VALUES (23, 'renan', '$2y$10$age3ndXdGJLuUF4PgGoI5OeScTnfZcsBEKze0ddcM2BaIx1d5/l4q', false, 'operador', '2026-03-19 20:40:01.028774-03', false, NULL, NULL, NULL, NULL);
INSERT INTO public.tb_usuarios OVERRIDING SYSTEM VALUE VALUES (32, 'lucas', '$2y$10$wqbujqRoXE6kM07kEJwU9eCp05Oc4tOkQxoIzN9HRoeRa6JG2FAgq', false, 'desenvolvedor', '2026-03-20 10:28:09.786803-03', false, NULL, NULL, NULL, NULL);
INSERT INTO public.tb_usuarios OVERRIDING SYSTEM VALUE VALUES (1, 'admin', '$2y$10$iMx/U2lqUH9kmTNL142SouQB.jvBrxTx.obLq3woEf0R5BVEFV23G', false, 'super_admin', '2026-02-02 11:08:48.320843-03', false, NULL, NULL, NULL, NULL);
INSERT INTO public.tb_usuarios OVERRIDING SYSTEM VALUE VALUES (13, 'caio', '$2y$10$1Yn0hpZFzfi81gs0x16Pquw1C/AJbn09NX.7aVBdiPhxoE4Kynnr2', false, 'admin', '2026-03-19 20:06:16.02098-03', false, 'Caio Vinícius', 'caio.vinicius.dmc@gmail.com', '121.018.404-48', NULL);
INSERT INTO public.tb_usuarios OVERRIDING SYSTEM VALUE VALUES (3, 'leo', '$2y$10$age3ndXdGJLuUF4PgGoI5OeScTnfZcsBEKze0ddcM2BaIx1d5/l4q', false, 'desenvolvedor', '2026-02-03 09:27:27.440711-03', false, NULL, NULL, NULL, NULL);
INSERT INTO public.tb_usuarios OVERRIDING SYSTEM VALUE VALUES (107, 'maria_infra', '$2y$10$age3ndXdGJLuUF4PgGoI5OeScTnfZcsBEKze0ddcM2BaIx1d5/l4q', false, 'desenvolvedor', '2026-03-20 14:06:58.111687-03', false, NULL, NULL, NULL, NULL);
INSERT INTO public.tb_usuarios OVERRIDING SYSTEM VALUE VALUES (108, 'joao_saude', '$2y$10$age3ndXdGJLuUF4PgGoI5OeScTnfZcsBEKze0ddcM2BaIx1d5/l4q', false, 'operador', '2026-03-20 14:06:58.115963-03', false, NULL, NULL, NULL, NULL);
INSERT INTO public.tb_usuarios OVERRIDING SYSTEM VALUE VALUES (109, 'ana_multi', '$2y$10$age3ndXdGJLuUF4PgGoI5OeScTnfZcsBEKze0ddcM2BaIx1d5/l4q', false, 'admin', '2026-03-20 14:06:58.122652-03', false, NULL, NULL, NULL, NULL);
INSERT INTO public.tb_usuarios OVERRIDING SYSTEM VALUE VALUES (110, 'pedro_agencia', '$2y$10$age3ndXdGJLuUF4PgGoI5OeScTnfZcsBEKze0ddcM2BaIx1d5/l4q', false, 'desenvolvedor', '2026-03-20 14:06:58.125863-03', false, NULL, NULL, NULL, NULL);


--
-- TOC entry 5461 (class 0 OID 45756)
-- Dependencies: 249
-- Data for Name: tb_valores_capturados; Type: TABLE DATA; Schema: public; Owner: postgres
--

INSERT INTO public.tb_valores_capturados VALUES (3, 9, 18, '1', '1', NULL, true, false, NULL, '2026-03-19 13:39:08.161614-03');


--
-- TOC entry 5498 (class 0 OID 46535)
-- Dependencies: 286
-- Data for Name: tb_webhooks; Type: TABLE DATA; Schema: public; Owner: postgres
--



--
-- TOC entry 5453 (class 0 OID 45658)
-- Dependencies: 240
-- Data for Name: tb_worker_heartbeat; Type: TABLE DATA; Schema: public; Owner: postgres
--



--
-- TOC entry 5467 (class 0 OID 45822)
-- Dependencies: 255
-- Data for Name: tb_workflow_edges; Type: TABLE DATA; Schema: public; Owner: postgres
--

INSERT INTO public.tb_workflow_edges VALUES (1, 2, 'edge_1', 'node_1', 'node_2', 'always', NULL, 'Sucesso', '[]', '2026-03-19 13:34:34.149865-03');
INSERT INTO public.tb_workflow_edges VALUES (2, 3, 'edge_1', 'node_1', 'node_2', 'always', NULL, 'Sucesso', '[]', '2026-03-19 13:34:37.037335-03');
INSERT INTO public.tb_workflow_edges VALUES (3, 4, 'edge_1', 'node_1', 'node_2', 'always', NULL, 'Sucesso', '[]', '2026-03-19 13:34:41.607354-03');
INSERT INTO public.tb_workflow_edges VALUES (4, 4, 'edge_2', 'node_2', 'node_3', 'always', NULL, 'Sucesso', '[]', '2026-03-19 13:34:41.607354-03');
INSERT INTO public.tb_workflow_edges VALUES (5, 5, 'edge_1', 'node_1', 'node_3', 'always', NULL, 'PG Pronto', '[]', '2026-03-19 13:34:47.190834-03');
INSERT INTO public.tb_workflow_edges VALUES (6, 5, 'edge_2', 'node_2', 'node_3', 'always', NULL, 'MY Pronto', '[]', '2026-03-19 13:34:47.190834-03');
INSERT INTO public.tb_workflow_edges VALUES (7, 6, 'edge_1', 'node_1', 'node_2', 'always', NULL, 'Branch A', '[]', '2026-03-19 13:34:55.051334-03');
INSERT INTO public.tb_workflow_edges VALUES (8, 6, 'edge_2', 'node_1', 'node_3', 'always', NULL, 'Branch B', '[]', '2026-03-19 13:34:55.051334-03');
INSERT INTO public.tb_workflow_edges VALUES (9, 6, 'edge_3', 'node_2', 'node_4', 'always', NULL, 'Merge', '[]', '2026-03-19 13:34:55.051334-03');
INSERT INTO public.tb_workflow_edges VALUES (10, 6, 'edge_4', 'node_3', 'node_4', 'always', NULL, 'Merge', '[]', '2026-03-19 13:34:55.051334-03');
INSERT INTO public.tb_workflow_edges VALUES (11, 7, 'edge_1', 'node_1', 'node_2', 'always', NULL, NULL, '[]', '2026-03-19 13:34:55.260046-03');
INSERT INTO public.tb_workflow_edges VALUES (12, 7, 'edge_2', 'node_2', 'node_3', 'always', NULL, NULL, '[]', '2026-03-19 13:34:55.260046-03');
INSERT INTO public.tb_workflow_edges VALUES (13, 7, 'edge_3', 'node_3', 'node_4', 'always', NULL, NULL, '[]', '2026-03-19 13:34:55.260046-03');
INSERT INTO public.tb_workflow_edges VALUES (14, 7, 'edge_4', 'node_4', 'node_5', 'always', NULL, NULL, '[]', '2026-03-19 13:34:55.260046-03');


--
-- TOC entry 5469 (class 0 OID 45844)
-- Dependencies: 257
-- Data for Name: tb_workflow_execucoes; Type: TABLE DATA; Schema: public; Owner: postgres
--

INSERT INTO public.tb_workflow_execucoes VALUES (3, 1, 1, 'completed', 'manual', '{"manual": true, "timestamp": "2026-03-18T16:04:38+01:00"}', '[]', '2026-03-18 12:04:38.116528-03', '2026-03-18 12:04:38.136677-03', 20, 3, 3, 3, 0, 0, '{"node_1": {"erro": null, "status": "completed", "resultado": {"tipo": "custom", "status": "unknown"}, "duracao_ms": 0}, "node_2": {"erro": null, "status": "completed", "resultado": {"tipo": "custom", "status": "unknown"}, "duracao_ms": 0}, "node_3": {"erro": null, "status": "completed", "resultado": {"tipo": "custom", "status": "unknown"}, "duracao_ms": 0}}', NULL, NULL);
INSERT INTO public.tb_workflow_execucoes VALUES (1, 1, 1, 'cancelled', 'manual', '{"manual": true, "timestamp": "2026-03-18T16:03:40+01:00"}', '[]', '2026-03-18 12:03:40.729556-03', '2026-03-18 13:55:26.125715-03', 6705396, 3, 0, 0, 0, 0, '{}', NULL, NULL);
INSERT INTO public.tb_workflow_execucoes VALUES (2, 1, 1, 'cancelled', 'manual', '{"manual": true, "timestamp": "2026-03-18T16:03:45+01:00"}', '[]', '2026-03-18 12:03:45.370105-03', '2026-03-18 13:55:26.310375-03', 6700940, 3, 0, 0, 0, 0, '{}', NULL, NULL);
INSERT INTO public.tb_workflow_execucoes VALUES (5, 3, 1, 'completed', 'manual', '{"manual": true, "timestamp": "2026-03-19T17:36:59+01:00"}', '[]', '2026-03-19 13:36:59.053718-03', '2026-03-19 13:36:59.065867-03', 12, 2, 2, 2, 0, 0, '{"node_1": {"erro": null, "status": "completed", "resultado": {"tipo": "custom", "status": "unknown"}, "duracao_ms": 0}, "node_2": {"erro": null, "status": "completed", "resultado": {"tipo": "custom", "status": "unknown"}, "duracao_ms": 0}}', NULL, NULL);
INSERT INTO public.tb_workflow_execucoes VALUES (6, 4, 1, 'completed', 'manual', '{"manual": true, "timestamp": "2026-03-19T17:36:59+01:00"}', '[]', '2026-03-19 13:36:59.243402-03', '2026-03-19 13:36:59.257139-03', 14, 3, 3, 3, 0, 0, '{"node_1": {"erro": null, "status": "completed", "resultado": {"tipo": "custom", "status": "unknown"}, "duracao_ms": 0}, "node_2": {"erro": null, "status": "completed", "resultado": {"tipo": "custom", "status": "unknown"}, "duracao_ms": 0}, "node_3": {"erro": null, "status": "completed", "resultado": {"tipo": "custom", "status": "unknown"}, "duracao_ms": 0}}', NULL, NULL);
INSERT INTO public.tb_workflow_execucoes VALUES (7, 5, 1, 'completed', 'manual', '{"manual": true, "timestamp": "2026-03-19T17:36:59+01:00"}', '[]', '2026-03-19 13:36:59.462196-03', '2026-03-19 13:36:59.47136-03', 9, 3, 2, 2, 0, 0, '{"node_1": {"erro": null, "status": "completed", "resultado": {"tipo": "custom", "status": "unknown"}, "duracao_ms": 0}, "node_3": {"erro": null, "status": "completed", "resultado": {"tipo": "custom", "status": "unknown"}, "duracao_ms": 0}}', NULL, NULL);
INSERT INTO public.tb_workflow_execucoes VALUES (8, 6, 1, 'completed', 'manual', '{"manual": true, "timestamp": "2026-03-19T17:36:59+01:00"}', '[]', '2026-03-19 13:36:59.664818-03', '2026-03-19 13:36:59.68075-03', 16, 4, 4, 4, 0, 0, '{"node_1": {"erro": null, "status": "completed", "resultado": {"tipo": "custom", "status": "unknown"}, "duracao_ms": 0}, "node_2": {"erro": null, "status": "completed", "resultado": {"tipo": "custom", "status": "unknown"}, "duracao_ms": 0}, "node_3": {"erro": null, "status": "completed", "resultado": {"tipo": "custom", "status": "unknown"}, "duracao_ms": 0}, "node_4": {"erro": null, "status": "completed", "resultado": {"tipo": "custom", "status": "unknown"}, "duracao_ms": 0}}', NULL, NULL);
INSERT INTO public.tb_workflow_execucoes VALUES (9, 7, 1, 'completed', 'manual', '{"manual": true, "timestamp": "2026-03-19T17:36:59+01:00"}', '[]', '2026-03-19 13:36:59.913902-03', '2026-03-19 13:36:59.932383-03', 18, 5, 5, 5, 0, 0, '{"node_1": {"erro": null, "status": "completed", "resultado": {"tipo": "custom", "status": "unknown"}, "duracao_ms": 0}, "node_2": {"erro": null, "status": "completed", "resultado": {"tipo": "custom", "status": "unknown"}, "duracao_ms": 0}, "node_3": {"erro": null, "status": "completed", "resultado": {"tipo": "custom", "status": "unknown"}, "duracao_ms": 0}, "node_4": {"erro": null, "status": "completed", "resultado": {"tipo": "custom", "status": "unknown"}, "duracao_ms": 0}, "node_5": {"erro": null, "status": "completed", "resultado": {"tipo": "custom", "status": "unknown"}, "duracao_ms": 0}}', NULL, NULL);
INSERT INTO public.tb_workflow_execucoes VALUES (10, 3, 1, 'completed', 'manual', '{"manual": true, "timestamp": "2026-03-19T17:37:05+01:00"}', '[]', '2026-03-19 13:37:05.793422-03', '2026-03-19 13:37:05.799198-03', 6, 2, 2, 2, 0, 0, '{"node_1": {"erro": null, "status": "completed", "resultado": {"tipo": "custom", "status": "unknown"}, "duracao_ms": 0}, "node_2": {"erro": null, "status": "completed", "resultado": {"tipo": "custom", "status": "unknown"}, "duracao_ms": 0}}', NULL, NULL);
INSERT INTO public.tb_workflow_execucoes VALUES (11, 4, 1, 'completed', 'manual', '{"manual": true, "timestamp": "2026-03-19T17:37:09+01:00"}', '[]', '2026-03-19 13:37:09.58747-03', '2026-03-19 13:37:09.599371-03', 12, 3, 3, 3, 0, 0, '{"node_1": {"erro": null, "status": "completed", "resultado": {"tipo": "custom", "status": "unknown"}, "duracao_ms": 0}, "node_2": {"erro": null, "status": "completed", "resultado": {"tipo": "custom", "status": "unknown"}, "duracao_ms": 0}, "node_3": {"erro": null, "status": "completed", "resultado": {"tipo": "custom", "status": "unknown"}, "duracao_ms": 0}}', NULL, NULL);
INSERT INTO public.tb_workflow_execucoes VALUES (12, 5, 1, 'completed', 'manual', '{"manual": true, "timestamp": "2026-03-19T17:37:09+01:00"}', '[]', '2026-03-19 13:37:09.811934-03', '2026-03-19 13:37:09.822024-03', 10, 3, 2, 2, 0, 0, '{"node_1": {"erro": null, "status": "completed", "resultado": {"tipo": "custom", "status": "unknown"}, "duracao_ms": 0}, "node_3": {"erro": null, "status": "completed", "resultado": {"tipo": "custom", "status": "unknown"}, "duracao_ms": 0}}', NULL, NULL);
INSERT INTO public.tb_workflow_execucoes VALUES (13, 6, 1, 'completed', 'manual', '{"manual": true, "timestamp": "2026-03-19T17:37:10+01:00"}', '[]', '2026-03-19 13:37:10.038874-03', '2026-03-19 13:37:10.057298-03', 18, 4, 4, 4, 0, 0, '{"node_1": {"erro": null, "status": "completed", "resultado": {"tipo": "custom", "status": "unknown"}, "duracao_ms": 0}, "node_2": {"erro": null, "status": "completed", "resultado": {"tipo": "custom", "status": "unknown"}, "duracao_ms": 0}, "node_3": {"erro": null, "status": "completed", "resultado": {"tipo": "custom", "status": "unknown"}, "duracao_ms": 0}, "node_4": {"erro": null, "status": "completed", "resultado": {"tipo": "custom", "status": "unknown"}, "duracao_ms": 0}}', NULL, NULL);
INSERT INTO public.tb_workflow_execucoes VALUES (14, 7, 1, 'completed', 'manual', '{"manual": true, "timestamp": "2026-03-19T17:37:10+01:00"}', '[]', '2026-03-19 13:37:10.362952-03', '2026-03-19 13:37:10.385218-03', 22, 5, 5, 5, 0, 0, '{"node_1": {"erro": null, "status": "completed", "resultado": {"tipo": "custom", "status": "unknown"}, "duracao_ms": 0}, "node_2": {"erro": null, "status": "completed", "resultado": {"tipo": "custom", "status": "unknown"}, "duracao_ms": 0}, "node_3": {"erro": null, "status": "completed", "resultado": {"tipo": "custom", "status": "unknown"}, "duracao_ms": 0}, "node_4": {"erro": null, "status": "completed", "resultado": {"tipo": "custom", "status": "unknown"}, "duracao_ms": 0}, "node_5": {"erro": null, "status": "completed", "resultado": {"tipo": "custom", "status": "unknown"}, "duracao_ms": 0}}', NULL, NULL);


--
-- TOC entry 5471 (class 0 OID 45872)
-- Dependencies: 259
-- Data for Name: tb_workflow_node_execucoes; Type: TABLE DATA; Schema: public; Owner: postgres
--

INSERT INTO public.tb_workflow_node_execucoes VALUES (1, 3, 'node_1', 'custom', 'Nó', 'completed', '2026-03-18 12:04:38.122407-03', '2026-03-18 12:04:38.127333-03', 0, '{"contexto": []}', '{"tipo": "custom", "status": "unknown"}', NULL, 0, 0);
INSERT INTO public.tb_workflow_node_execucoes VALUES (2, 3, 'node_2', 'custom', 'Nó', 'completed', '2026-03-18 12:04:38.128623-03', '2026-03-18 12:04:38.129615-03', 0, '{"contexto": {"nodes": {"node_1": {"tipo": "custom", "status": "unknown"}}}}', '{"tipo": "custom", "status": "unknown"}', NULL, 1, 0);
INSERT INTO public.tb_workflow_node_execucoes VALUES (3, 3, 'node_3', 'custom', 'Nó', 'completed', '2026-03-18 12:04:38.130695-03', '2026-03-18 12:04:38.131629-03', 0, '{"contexto": {"nodes": {"node_1": {"tipo": "custom", "status": "unknown"}, "node_2": {"tipo": "custom", "status": "unknown"}}}}', '{"tipo": "custom", "status": "unknown"}', NULL, 2, 0);
INSERT INTO public.tb_workflow_node_execucoes VALUES (7, 5, 'node_1', 'custom', 'Chamados Recentes', 'completed', '2026-03-19 13:36:59.058079-03', '2026-03-19 13:36:59.060973-03', 0, '{"contexto": []}', '{"tipo": "custom", "status": "unknown"}', NULL, 0, 0);
INSERT INTO public.tb_workflow_node_execucoes VALUES (8, 5, 'node_2', 'custom', 'Status Chamados', 'completed', '2026-03-19 13:36:59.062922-03', '2026-03-19 13:36:59.063837-03', 0, '{"contexto": {"nodes": {"node_1": {"tipo": "custom", "status": "unknown"}}}}', '{"tipo": "custom", "status": "unknown"}', NULL, 1, 0);
INSERT INTO public.tb_workflow_node_execucoes VALUES (9, 6, 'node_1', 'custom', 'Moradores', 'completed', '2026-03-19 13:36:59.24699-03', '2026-03-19 13:36:59.249552-03', 0, '{"contexto": []}', '{"tipo": "custom", "status": "unknown"}', NULL, 0, 0);
INSERT INTO public.tb_workflow_node_execucoes VALUES (10, 6, 'node_2', 'custom', 'Avisos', 'completed', '2026-03-19 13:36:59.251381-03', '2026-03-19 13:36:59.252238-03', 0, '{"contexto": {"nodes": {"node_1": {"tipo": "custom", "status": "unknown"}}}}', '{"tipo": "custom", "status": "unknown"}', NULL, 1, 0);
INSERT INTO public.tb_workflow_node_execucoes VALUES (11, 6, 'node_3', 'custom', 'Areas Comuns', 'completed', '2026-03-19 13:36:59.253799-03', '2026-03-19 13:36:59.254922-03', 0, '{"contexto": {"nodes": {"node_1": {"tipo": "custom", "status": "unknown"}, "node_2": {"tipo": "custom", "status": "unknown"}}}}', '{"tipo": "custom", "status": "unknown"}', NULL, 2, 0);
INSERT INTO public.tb_workflow_node_execucoes VALUES (12, 7, 'node_1', 'custom', 'PG Chamados', 'completed', '2026-03-19 13:36:59.465413-03', '2026-03-19 13:36:59.467464-03', 0, '{"contexto": []}', '{"tipo": "custom", "status": "unknown"}', NULL, 0, 0);
INSERT INTO public.tb_workflow_node_execucoes VALUES (13, 7, 'node_3', 'custom', 'PG Audit', 'completed', '2026-03-19 13:36:59.468987-03', '2026-03-19 13:36:59.469763-03', 0, '{"contexto": {"nodes": {"node_1": {"tipo": "custom", "status": "unknown"}}}}', '{"tipo": "custom", "status": "unknown"}', NULL, 1, 0);
INSERT INTO public.tb_workflow_node_execucoes VALUES (14, 8, 'node_1', 'custom', 'Config PG', 'completed', '2026-03-19 13:36:59.668215-03', '2026-03-19 13:36:59.670502-03', 0, '{"contexto": []}', '{"tipo": "custom", "status": "unknown"}', NULL, 0, 0);
INSERT INTO public.tb_workflow_node_execucoes VALUES (15, 8, 'node_2', 'custom', 'Setores PG', 'completed', '2026-03-19 13:36:59.672887-03', '2026-03-19 13:36:59.674039-03', 0, '{"contexto": {"nodes": {"node_1": {"tipo": "custom", "status": "unknown"}}}}', '{"tipo": "custom", "status": "unknown"}', NULL, 1, 0);
INSERT INTO public.tb_workflow_node_execucoes VALUES (16, 8, 'node_4', 'custom', 'Roles MY', 'completed', '2026-03-19 13:36:59.675113-03', '2026-03-19 13:36:59.676351-03', 0, '{"contexto": {"nodes": {"node_1": {"tipo": "custom", "status": "unknown"}, "node_2": {"tipo": "custom", "status": "unknown"}}}}', '{"tipo": "custom", "status": "unknown"}', NULL, 2, 0);
INSERT INTO public.tb_workflow_node_execucoes VALUES (17, 8, 'node_3', 'custom', 'Anuncios MY', 'completed', '2026-03-19 13:36:59.678033-03', '2026-03-19 13:36:59.679017-03', 0, '{"contexto": {"nodes": {"node_1": {"tipo": "custom", "status": "unknown"}, "node_2": {"tipo": "custom", "status": "unknown"}, "node_4": {"tipo": "custom", "status": "unknown"}}}}', '{"tipo": "custom", "status": "unknown"}', NULL, 3, 0);
INSERT INTO public.tb_workflow_node_execucoes VALUES (18, 9, 'node_1', 'custom', 'PG Config', 'completed', '2026-03-19 13:36:59.91755-03', '2026-03-19 13:36:59.919853-03', 0, '{"contexto": []}', '{"tipo": "custom", "status": "unknown"}', NULL, 0, 0);
INSERT INTO public.tb_workflow_node_execucoes VALUES (19, 9, 'node_2', 'custom', 'MY Areas', 'completed', '2026-03-19 13:36:59.921775-03', '2026-03-19 13:36:59.922712-03', 0, '{"contexto": {"nodes": {"node_1": {"tipo": "custom", "status": "unknown"}}}}', '{"tipo": "custom", "status": "unknown"}', NULL, 1, 0);
INSERT INTO public.tb_workflow_node_execucoes VALUES (20, 9, 'node_3', 'custom', 'PG Audit', 'completed', '2026-03-19 13:36:59.923516-03', '2026-03-19 13:36:59.924403-03', 0, '{"contexto": {"nodes": {"node_1": {"tipo": "custom", "status": "unknown"}, "node_2": {"tipo": "custom", "status": "unknown"}}}}', '{"tipo": "custom", "status": "unknown"}', NULL, 2, 0);
INSERT INTO public.tb_workflow_node_execucoes VALUES (21, 9, 'node_4', 'custom', 'MY Roles', 'completed', '2026-03-19 13:36:59.92523-03', '2026-03-19 13:36:59.926496-03', 0, '{"contexto": {"nodes": {"node_1": {"tipo": "custom", "status": "unknown"}, "node_2": {"tipo": "custom", "status": "unknown"}, "node_3": {"tipo": "custom", "status": "unknown"}}}}', '{"tipo": "custom", "status": "unknown"}', NULL, 3, 0);
INSERT INTO public.tb_workflow_node_execucoes VALUES (22, 9, 'node_5', 'custom', 'PG Chamados', 'completed', '2026-03-19 13:36:59.928604-03', '2026-03-19 13:36:59.930095-03', 0, '{"contexto": {"nodes": {"node_1": {"tipo": "custom", "status": "unknown"}, "node_2": {"tipo": "custom", "status": "unknown"}, "node_3": {"tipo": "custom", "status": "unknown"}, "node_4": {"tipo": "custom", "status": "unknown"}}}}', '{"tipo": "custom", "status": "unknown"}', NULL, 4, 0);
INSERT INTO public.tb_workflow_node_execucoes VALUES (23, 10, 'node_1', 'custom', 'Chamados Recentes', 'completed', '2026-03-19 13:37:05.795461-03', '2026-03-19 13:37:05.796849-03', 0, '{"contexto": []}', '{"tipo": "custom", "status": "unknown"}', NULL, 0, 0);
INSERT INTO public.tb_workflow_node_execucoes VALUES (24, 10, 'node_2', 'custom', 'Status Chamados', 'completed', '2026-03-19 13:37:05.797883-03', '2026-03-19 13:37:05.798409-03', 0, '{"contexto": {"nodes": {"node_1": {"tipo": "custom", "status": "unknown"}}}}', '{"tipo": "custom", "status": "unknown"}', NULL, 1, 0);
INSERT INTO public.tb_workflow_node_execucoes VALUES (25, 11, 'node_1', 'custom', 'Moradores', 'completed', '2026-03-19 13:37:09.590453-03', '2026-03-19 13:37:09.592841-03', 0, '{"contexto": []}', '{"tipo": "custom", "status": "unknown"}', NULL, 0, 0);
INSERT INTO public.tb_workflow_node_execucoes VALUES (26, 11, 'node_2', 'custom', 'Avisos', 'completed', '2026-03-19 13:37:09.594576-03', '2026-03-19 13:37:09.59562-03', 0, '{"contexto": {"nodes": {"node_1": {"tipo": "custom", "status": "unknown"}}}}', '{"tipo": "custom", "status": "unknown"}', NULL, 1, 0);
INSERT INTO public.tb_workflow_node_execucoes VALUES (27, 11, 'node_3', 'custom', 'Areas Comuns', 'completed', '2026-03-19 13:37:09.596392-03', '2026-03-19 13:37:09.597302-03', 0, '{"contexto": {"nodes": {"node_1": {"tipo": "custom", "status": "unknown"}, "node_2": {"tipo": "custom", "status": "unknown"}}}}', '{"tipo": "custom", "status": "unknown"}', NULL, 2, 0);
INSERT INTO public.tb_workflow_node_execucoes VALUES (28, 12, 'node_1', 'custom', 'PG Chamados', 'completed', '2026-03-19 13:37:09.815146-03', '2026-03-19 13:37:09.817299-03', 0, '{"contexto": []}', '{"tipo": "custom", "status": "unknown"}', NULL, 0, 0);
INSERT INTO public.tb_workflow_node_execucoes VALUES (29, 12, 'node_3', 'custom', 'PG Audit', 'completed', '2026-03-19 13:37:09.819006-03', '2026-03-19 13:37:09.820322-03', 0, '{"contexto": {"nodes": {"node_1": {"tipo": "custom", "status": "unknown"}}}}', '{"tipo": "custom", "status": "unknown"}', NULL, 1, 0);
INSERT INTO public.tb_workflow_node_execucoes VALUES (30, 13, 'node_1', 'custom', 'Config PG', 'completed', '2026-03-19 13:37:10.042043-03', '2026-03-19 13:37:10.044412-03', 0, '{"contexto": []}', '{"tipo": "custom", "status": "unknown"}', NULL, 0, 0);
INSERT INTO public.tb_workflow_node_execucoes VALUES (31, 13, 'node_2', 'custom', 'Setores PG', 'completed', '2026-03-19 13:37:10.047209-03', '2026-03-19 13:37:10.048381-03', 0, '{"contexto": {"nodes": {"node_1": {"tipo": "custom", "status": "unknown"}}}}', '{"tipo": "custom", "status": "unknown"}', NULL, 1, 0);
INSERT INTO public.tb_workflow_node_execucoes VALUES (32, 13, 'node_4', 'custom', 'Roles MY', 'completed', '2026-03-19 13:37:10.050071-03', '2026-03-19 13:37:10.051474-03', 0, '{"contexto": {"nodes": {"node_1": {"tipo": "custom", "status": "unknown"}, "node_2": {"tipo": "custom", "status": "unknown"}}}}', '{"tipo": "custom", "status": "unknown"}', NULL, 2, 0);
INSERT INTO public.tb_workflow_node_execucoes VALUES (33, 13, 'node_3', 'custom', 'Anuncios MY', 'completed', '2026-03-19 13:37:10.052978-03', '2026-03-19 13:37:10.055396-03', 0, '{"contexto": {"nodes": {"node_1": {"tipo": "custom", "status": "unknown"}, "node_2": {"tipo": "custom", "status": "unknown"}, "node_4": {"tipo": "custom", "status": "unknown"}}}}', '{"tipo": "custom", "status": "unknown"}', NULL, 3, 0);
INSERT INTO public.tb_workflow_node_execucoes VALUES (34, 14, 'node_1', 'custom', 'PG Config', 'completed', '2026-03-19 13:37:10.367762-03', '2026-03-19 13:37:10.371675-03', 0, '{"contexto": []}', '{"tipo": "custom", "status": "unknown"}', NULL, 0, 0);
INSERT INTO public.tb_workflow_node_execucoes VALUES (35, 14, 'node_2', 'custom', 'MY Areas', 'completed', '2026-03-19 13:37:10.374404-03', '2026-03-19 13:37:10.375706-03', 0, '{"contexto": {"nodes": {"node_1": {"tipo": "custom", "status": "unknown"}}}}', '{"tipo": "custom", "status": "unknown"}', NULL, 1, 0);
INSERT INTO public.tb_workflow_node_execucoes VALUES (36, 14, 'node_3', 'custom', 'PG Audit', 'completed', '2026-03-19 13:37:10.376767-03', '2026-03-19 13:37:10.378052-03', 0, '{"contexto": {"nodes": {"node_1": {"tipo": "custom", "status": "unknown"}, "node_2": {"tipo": "custom", "status": "unknown"}}}}', '{"tipo": "custom", "status": "unknown"}', NULL, 2, 0);
INSERT INTO public.tb_workflow_node_execucoes VALUES (37, 14, 'node_4', 'custom', 'MY Roles', 'completed', '2026-03-19 13:37:10.379265-03', '2026-03-19 13:37:10.380491-03', 0, '{"contexto": {"nodes": {"node_1": {"tipo": "custom", "status": "unknown"}, "node_2": {"tipo": "custom", "status": "unknown"}, "node_3": {"tipo": "custom", "status": "unknown"}}}}', '{"tipo": "custom", "status": "unknown"}', NULL, 3, 0);
INSERT INTO public.tb_workflow_node_execucoes VALUES (38, 14, 'node_5', 'custom', 'PG Chamados', 'completed', '2026-03-19 13:37:10.381271-03', '2026-03-19 13:37:10.382298-03', 0, '{"contexto": {"nodes": {"node_1": {"tipo": "custom", "status": "unknown"}, "node_2": {"tipo": "custom", "status": "unknown"}, "node_3": {"tipo": "custom", "status": "unknown"}, "node_4": {"tipo": "custom", "status": "unknown"}}}}', '{"tipo": "custom", "status": "unknown"}', NULL, 4, 0);


--
-- TOC entry 5465 (class 0 OID 45799)
-- Dependencies: 253
-- Data for Name: tb_workflow_nodes; Type: TABLE DATA; Schema: public; Owner: postgres
--

INSERT INTO public.tb_workflow_nodes VALUES (1, 2, 'node_1', 'custom', 'Chamados Recentes', 32, 100, 200, '{"label": "Chamados Recentes", "id_referencia": 32}', 0, '2026-03-19 13:34:34.149865-03');
INSERT INTO public.tb_workflow_nodes VALUES (2, 2, 'node_2', 'custom', 'Status Chamados', 33, 400, 200, '{"label": "Status Chamados", "id_referencia": 33}', 1, '2026-03-19 13:34:34.149865-03');
INSERT INTO public.tb_workflow_nodes VALUES (3, 3, 'node_1', 'custom', 'Chamados Recentes', 32, 100, 200, '{"label": "Chamados Recentes", "id_referencia": 32}', 0, '2026-03-19 13:34:37.037335-03');
INSERT INTO public.tb_workflow_nodes VALUES (4, 3, 'node_2', 'custom', 'Status Chamados', 33, 400, 200, '{"label": "Status Chamados", "id_referencia": 33}', 1, '2026-03-19 13:34:37.037335-03');
INSERT INTO public.tb_workflow_nodes VALUES (5, 4, 'node_1', 'custom', 'Moradores', 39, 100, 200, '{"label": "Moradores", "id_referencia": 39}', 0, '2026-03-19 13:34:41.607354-03');
INSERT INTO public.tb_workflow_nodes VALUES (6, 4, 'node_2', 'custom', 'Avisos', 40, 400, 200, '{"label": "Avisos", "id_referencia": 40}', 1, '2026-03-19 13:34:41.607354-03');
INSERT INTO public.tb_workflow_nodes VALUES (7, 4, 'node_3', 'custom', 'Areas Comuns', 44, 700, 200, '{"label": "Areas Comuns", "id_referencia": 44}', 2, '2026-03-19 13:34:41.607354-03');
INSERT INTO public.tb_workflow_nodes VALUES (8, 5, 'node_1', 'custom', 'PG Chamados', 32, 100, 100, '{"label": "PG Chamados", "id_referencia": 32}', 0, '2026-03-19 13:34:47.190834-03');
INSERT INTO public.tb_workflow_nodes VALUES (9, 5, 'node_2', 'custom', 'MY Moradores', 39, 100, 400, '{"label": "MY Moradores", "id_referencia": 39}', 1, '2026-03-19 13:34:47.190834-03');
INSERT INTO public.tb_workflow_nodes VALUES (10, 5, 'node_3', 'custom', 'PG Audit', 38, 400, 250, '{"label": "PG Audit", "id_referencia": 38}', 2, '2026-03-19 13:34:47.190834-03');
INSERT INTO public.tb_workflow_nodes VALUES (11, 6, 'node_1', 'custom', 'Config PG', 34, 100, 300, '{"label": "Config PG", "id_referencia": 34}', 0, '2026-03-19 13:34:55.051334-03');
INSERT INTO public.tb_workflow_nodes VALUES (12, 6, 'node_2', 'custom', 'Setores PG', 37, 400, 150, '{"label": "Setores PG", "id_referencia": 37}', 1, '2026-03-19 13:34:55.051334-03');
INSERT INTO public.tb_workflow_nodes VALUES (13, 6, 'node_3', 'custom', 'Anuncios MY', 45, 400, 450, '{"label": "Anuncios MY", "id_referencia": 45}', 2, '2026-03-19 13:34:55.051334-03');
INSERT INTO public.tb_workflow_nodes VALUES (14, 6, 'node_4', 'custom', 'Roles MY', 46, 700, 300, '{"label": "Roles MY", "id_referencia": 46}', 3, '2026-03-19 13:34:55.051334-03');
INSERT INTO public.tb_workflow_nodes VALUES (15, 7, 'node_1', 'custom', 'PG Config', 34, 100, 200, '{"label": "PG Config", "id_referencia": 34}', 0, '2026-03-19 13:34:55.260046-03');
INSERT INTO public.tb_workflow_nodes VALUES (16, 7, 'node_2', 'custom', 'MY Areas', 44, 300, 200, '{"label": "MY Areas", "id_referencia": 44}', 1, '2026-03-19 13:34:55.260046-03');
INSERT INTO public.tb_workflow_nodes VALUES (17, 7, 'node_3', 'custom', 'PG Audit', 38, 500, 200, '{"label": "PG Audit", "id_referencia": 38}', 2, '2026-03-19 13:34:55.260046-03');
INSERT INTO public.tb_workflow_nodes VALUES (18, 7, 'node_4', 'custom', 'MY Roles', 46, 700, 200, '{"label": "MY Roles", "id_referencia": 46}', 3, '2026-03-19 13:34:55.260046-03');
INSERT INTO public.tb_workflow_nodes VALUES (19, 7, 'node_5', 'custom', 'PG Chamados', 32, 900, 200, '{"label": "PG Chamados", "id_referencia": 32}', 4, '2026-03-19 13:34:55.260046-03');


--
-- TOC entry 5463 (class 0 OID 45781)
-- Dependencies: 251
-- Data for Name: tb_workflows; Type: TABLE DATA; Schema: public; Owner: postgres
--

INSERT INTO public.tb_workflows VALUES (1, 'Workflow de Teste', 'Workflow criado automaticamente para testes', true, '{"edges": [{"id": "edge_1", "source": "node_1", "target": "node_2", "condicao": "always"}, {"id": "edge_2", "source": "node_2", "target": "node_3", "condicao": "always"}], "nodes": [{"id": "node_1", "tipo": "trigger", "label": "Início", "posicao": {"x": 100, "y": 50}, "configuracao": []}, {"id": "node_2", "tipo": "notification", "label": "Notificar", "posicao": {"x": 100, "y": 200}, "configuracao": {"tipo": "log", "mensagem": "Workflow executado!"}}, {"id": "node_3", "tipo": "end", "label": "Fim", "posicao": {"x": 100, "y": 350}, "configuracao": {"status": "success"}}]}', 1, 'api_event', '{}', 1, '2026-02-04 17:00:25.569394-03', '2026-02-04 17:00:25.569394-03');
INSERT INTO public.tb_workflows VALUES (2, 'WF_Teste_01_PG_Sequencial', 'Workflow de teste: rotinas PG em sequencia', true, '{"edges": [{"id": "edge_1", "data": {"condition": "always", "expression": null}, "label": "Sucesso", "style": [], "source": "node_1", "target": "node_2"}], "nodes": [{"id": "node_1", "data": {"label": "Chamados Recentes", "id_referencia": 32}, "type": "custom", "position": {"x": 100, "y": 200}}, {"id": "node_2", "data": {"label": "Status Chamados", "id_referencia": 33}, "type": "custom", "position": {"x": 400, "y": 200}}]}', 1, 'manual', '[]', 1, '2026-03-19 13:34:34.149865-03', '2026-03-19 13:34:34.149865-03');
INSERT INTO public.tb_workflows VALUES (3, 'WF_Teste_01_PG_Sequencial', 'Workflow de teste: rotinas PG em sequencia', true, '{"edges": [{"id": "edge_1", "data": {"condition": "always", "expression": null}, "label": "Sucesso", "style": [], "source": "node_1", "target": "node_2"}], "nodes": [{"id": "node_1", "data": {"label": "Chamados Recentes", "id_referencia": 32}, "type": "custom", "position": {"x": 100, "y": 200}}, {"id": "node_2", "data": {"label": "Status Chamados", "id_referencia": 33}, "type": "custom", "position": {"x": 400, "y": 200}}]}', 1, 'manual', '[]', 1, '2026-03-19 13:34:37.037335-03', '2026-03-19 13:34:37.037335-03');
INSERT INTO public.tb_workflows VALUES (4, 'WF_Teste_02_MY_Cadeia', 'Workflow MySQL: moradores avisos e areas comuns', true, '{"edges": [{"id": "edge_1", "data": {"condition": "always"}, "label": "Sucesso", "style": [], "source": "node_1", "target": "node_2"}, {"id": "edge_2", "data": {"condition": "always"}, "label": "Sucesso", "style": [], "source": "node_2", "target": "node_3"}], "nodes": [{"id": "node_1", "data": {"label": "Moradores", "id_referencia": 39}, "type": "custom", "position": {"x": 100, "y": 200}}, {"id": "node_2", "data": {"label": "Avisos", "id_referencia": 40}, "type": "custom", "position": {"x": 400, "y": 200}}, {"id": "node_3", "data": {"label": "Areas Comuns", "id_referencia": 44}, "type": "custom", "position": {"x": 700, "y": 200}}]}', 1, 'manual', '[]', 1, '2026-03-19 13:34:41.607354-03', '2026-03-19 13:34:41.607354-03');
INSERT INTO public.tb_workflows VALUES (5, 'WF_Teste_03_Misto_Paralelo', 'Workflow misto com rotinas PG e MY em paralelo', true, '{"edges": [{"id": "edge_1", "data": {"condition": "always"}, "label": "PG Pronto", "style": [], "source": "node_1", "target": "node_3"}, {"id": "edge_2", "data": {"condition": "always"}, "label": "MY Pronto", "style": [], "source": "node_2", "target": "node_3"}], "nodes": [{"id": "node_1", "data": {"label": "PG Chamados", "id_referencia": 32}, "type": "custom", "position": {"x": 100, "y": 100}}, {"id": "node_2", "data": {"label": "MY Moradores", "id_referencia": 39}, "type": "custom", "position": {"x": 100, "y": 400}}, {"id": "node_3", "data": {"label": "PG Audit", "id_referencia": 38}, "type": "custom", "position": {"x": 400, "y": 250}}]}', 1, 'manual', '[]', 1, '2026-03-19 13:34:47.190834-03', '2026-03-19 13:34:47.190834-03');
INSERT INTO public.tb_workflows VALUES (6, 'WF_Teste_04_Complexo_Branching', 'Workflow complexo com branching condicional', true, '{"edges": [{"id": "edge_1", "data": {"condition": "always"}, "label": "Branch A", "style": [], "source": "node_1", "target": "node_2"}, {"id": "edge_2", "data": {"condition": "always"}, "label": "Branch B", "style": [], "source": "node_1", "target": "node_3"}, {"id": "edge_3", "data": {"condition": "always"}, "label": "Merge", "style": [], "source": "node_2", "target": "node_4"}, {"id": "edge_4", "data": {"condition": "always"}, "label": "Merge", "style": [], "source": "node_3", "target": "node_4"}], "nodes": [{"id": "node_1", "data": {"label": "Config PG", "id_referencia": 34}, "type": "custom", "position": {"x": 100, "y": 300}}, {"id": "node_2", "data": {"label": "Setores PG", "id_referencia": 37}, "type": "custom", "position": {"x": 400, "y": 150}}, {"id": "node_3", "data": {"label": "Anuncios MY", "id_referencia": 45}, "type": "custom", "position": {"x": 400, "y": 450}}, {"id": "node_4", "data": {"label": "Roles MY", "id_referencia": 46}, "type": "custom", "position": {"x": 700, "y": 300}}]}', 1, 'manual', '[]', 1, '2026-03-19 13:34:55.051334-03', '2026-03-19 13:34:55.051334-03');
INSERT INTO public.tb_workflows VALUES (7, 'WF_Teste_05_Cadeia_Completa', 'Workflow cadeia completa PG e MY intercalados', true, '{"edges": [{"id": "edge_1", "data": {"condition": "always"}, "style": [], "source": "node_1", "target": "node_2"}, {"id": "edge_2", "data": {"condition": "always"}, "style": [], "source": "node_2", "target": "node_3"}, {"id": "edge_3", "data": {"condition": "always"}, "style": [], "source": "node_3", "target": "node_4"}, {"id": "edge_4", "data": {"condition": "always"}, "style": [], "source": "node_4", "target": "node_5"}], "nodes": [{"id": "node_1", "data": {"label": "PG Config", "id_referencia": 34}, "type": "custom", "position": {"x": 100, "y": 200}}, {"id": "node_2", "data": {"label": "MY Areas", "id_referencia": 44}, "type": "custom", "position": {"x": 300, "y": 200}}, {"id": "node_3", "data": {"label": "PG Audit", "id_referencia": 38}, "type": "custom", "position": {"x": 500, "y": 200}}, {"id": "node_4", "data": {"label": "MY Roles", "id_referencia": 46}, "type": "custom", "position": {"x": 700, "y": 200}}, {"id": "node_5", "data": {"label": "PG Chamados", "id_referencia": 32}, "type": "custom", "position": {"x": 900, "y": 200}}]}', 1, 'manual', '[]', 1, '2026-03-19 13:34:55.260046-03', '2026-03-19 13:34:55.260046-03');


--
-- TOC entry 5558 (class 0 OID 0)
-- Dependencies: 221
-- Name: connections_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.connections_id_seq', 1, true);


--
-- TOC entry 5559 (class 0 OID 0)
-- Dependencies: 242
-- Name: logs_sistema_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.logs_sistema_id_seq', 661, true);


--
-- TOC entry 5560 (class 0 OID 0)
-- Dependencies: 223
-- Name: schedules_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.schedules_id_seq', 1, true);


--
-- TOC entry 5561 (class 0 OID 0)
-- Dependencies: 244
-- Name: tb_api_externas_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.tb_api_externas_id_seq', 25, true);


--
-- TOC entry 5562 (class 0 OID 0)
-- Dependencies: 217
-- Name: tb_arquivos_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.tb_arquivos_id_seq', 3, true);


--
-- TOC entry 5563 (class 0 OID 0)
-- Dependencies: 282
-- Name: tb_auditoria_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.tb_auditoria_id_seq', 1094, true);


--
-- TOC entry 5564 (class 0 OID 0)
-- Dependencies: 219
-- Name: tb_auditoria_rotina_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.tb_auditoria_rotina_id_seq', 5, true);


--
-- TOC entry 5565 (class 0 OID 0)
-- Dependencies: 291
-- Name: tb_backups_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.tb_backups_id_seq', 59, true);


--
-- TOC entry 5566 (class 0 OID 0)
-- Dependencies: 231
-- Name: tb_blocos_rotina_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.tb_blocos_rotina_id_seq', 65, true);


--
-- TOC entry 5567 (class 0 OID 0)
-- Dependencies: 289
-- Name: tb_canais_notificacao_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.tb_canais_notificacao_id_seq', 55, true);


--
-- TOC entry 5568 (class 0 OID 0)
-- Dependencies: 280
-- Name: tb_compartilhamentos_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.tb_compartilhamentos_id_seq', 1, false);


--
-- TOC entry 5569 (class 0 OID 0)
-- Dependencies: 268
-- Name: tb_empresas_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.tb_empresas_id_seq', 66, true);


--
-- TOC entry 5570 (class 0 OID 0)
-- Dependencies: 246
-- Name: tb_eventos_api_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.tb_eventos_api_id_seq', 13, true);


--
-- TOC entry 5571 (class 0 OID 0)
-- Dependencies: 287
-- Name: tb_fila_execucao_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.tb_fila_execucao_id_seq', 14, true);


--
-- TOC entry 5572 (class 0 OID 0)
-- Dependencies: 233
-- Name: tb_logs_execucao_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.tb_logs_execucao_id_seq', 113, true);


--
-- TOC entry 5573 (class 0 OID 0)
-- Dependencies: 235
-- Name: tb_logs_sistema_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.tb_logs_sistema_id_seq', 37, true);


--
-- TOC entry 5574 (class 0 OID 0)
-- Dependencies: 237
-- Name: tb_metricas_sistema_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.tb_metricas_sistema_id_seq', 1, false);


--
-- TOC entry 5575 (class 0 OID 0)
-- Dependencies: 264
-- Name: tb_notificacoes_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.tb_notificacoes_id_seq', 12, true);


--
-- TOC entry 5576 (class 0 OID 0)
-- Dependencies: 293
-- Name: tb_password_resets_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.tb_password_resets_id_seq', 3, true);


--
-- TOC entry 5577 (class 0 OID 0)
-- Dependencies: 227
-- Name: tb_perfis_conexao_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.tb_perfis_conexao_id_seq', 94, true);


--
-- TOC entry 5578 (class 0 OID 0)
-- Dependencies: 262
-- Name: tb_pipeline_execucoes_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.tb_pipeline_execucoes_id_seq', 19, true);


--
-- TOC entry 5579 (class 0 OID 0)
-- Dependencies: 260
-- Name: tb_pipelines_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.tb_pipelines_id_seq', 29, true);


--
-- TOC entry 5580 (class 0 OID 0)
-- Dependencies: 270
-- Name: tb_projetos_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.tb_projetos_id_seq', 124, true);


--
-- TOC entry 5581 (class 0 OID 0)
-- Dependencies: 266
-- Name: tb_rate_limits_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.tb_rate_limits_id_seq', 25, true);


--
-- TOC entry 5582 (class 0 OID 0)
-- Dependencies: 276
-- Name: tb_recurso_empresas_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.tb_recurso_empresas_id_seq', 94, true);


--
-- TOC entry 5583 (class 0 OID 0)
-- Dependencies: 278
-- Name: tb_recurso_projetos_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.tb_recurso_projetos_id_seq', 92, true);


--
-- TOC entry 5584 (class 0 OID 0)
-- Dependencies: 229
-- Name: tb_rotinas_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.tb_rotinas_id_seq', 65, true);


--
-- TOC entry 5585 (class 0 OID 0)
-- Dependencies: 272
-- Name: tb_usuario_empresas_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.tb_usuario_empresas_id_seq', 126, true);


--
-- TOC entry 5586 (class 0 OID 0)
-- Dependencies: 274
-- Name: tb_usuario_projetos_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.tb_usuario_projetos_id_seq', 209, true);


--
-- TOC entry 5587 (class 0 OID 0)
-- Dependencies: 225
-- Name: tb_usuarios_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.tb_usuarios_id_seq', 110, true);


--
-- TOC entry 5588 (class 0 OID 0)
-- Dependencies: 248
-- Name: tb_valores_capturados_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.tb_valores_capturados_id_seq', 3, true);


--
-- TOC entry 5589 (class 0 OID 0)
-- Dependencies: 285
-- Name: tb_webhooks_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.tb_webhooks_id_seq', 27, true);


--
-- TOC entry 5590 (class 0 OID 0)
-- Dependencies: 239
-- Name: tb_worker_heartbeat_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.tb_worker_heartbeat_id_seq', 1, false);


--
-- TOC entry 5591 (class 0 OID 0)
-- Dependencies: 254
-- Name: tb_workflow_edges_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.tb_workflow_edges_id_seq', 14, true);


--
-- TOC entry 5592 (class 0 OID 0)
-- Dependencies: 256
-- Name: tb_workflow_execucoes_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.tb_workflow_execucoes_id_seq', 14, true);


--
-- TOC entry 5593 (class 0 OID 0)
-- Dependencies: 258
-- Name: tb_workflow_node_execucoes_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.tb_workflow_node_execucoes_id_seq', 38, true);


--
-- TOC entry 5594 (class 0 OID 0)
-- Dependencies: 252
-- Name: tb_workflow_nodes_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.tb_workflow_nodes_id_seq', 19, true);


--
-- TOC entry 5595 (class 0 OID 0)
-- Dependencies: 250
-- Name: tb_workflows_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.tb_workflows_id_seq', 20, true);


--
-- TOC entry 5083 (class 2606 OID 45527)
-- Name: connections connections_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.connections
    ADD CONSTRAINT connections_pkey PRIMARY KEY (id);


--
-- TOC entry 5120 (class 2606 OID 45686)
-- Name: logs_sistema logs_sistema_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.logs_sistema
    ADD CONSTRAINT logs_sistema_pkey PRIMARY KEY (id);


--
-- TOC entry 5085 (class 2606 OID 45536)
-- Name: schedules schedules_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.schedules
    ADD CONSTRAINT schedules_pkey PRIMARY KEY (id);


--
-- TOC entry 5124 (class 2606 OID 45728)
-- Name: tb_api_externas tb_api_externas_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tb_api_externas
    ADD CONSTRAINT tb_api_externas_pkey PRIMARY KEY (id);


--
-- TOC entry 5079 (class 2606 OID 45493)
-- Name: tb_arquivos tb_arquivos_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tb_arquivos
    ADD CONSTRAINT tb_arquivos_pkey PRIMARY KEY (id);


--
-- TOC entry 5230 (class 2606 OID 46518)
-- Name: tb_auditoria tb_auditoria_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tb_auditoria
    ADD CONSTRAINT tb_auditoria_pkey PRIMARY KEY (id);


--
-- TOC entry 5081 (class 2606 OID 45502)
-- Name: tb_auditoria_rotina tb_auditoria_rotina_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tb_auditoria_rotina
    ADD CONSTRAINT tb_auditoria_rotina_pkey PRIMARY KEY (id);


--
-- TOC entry 5250 (class 2606 OID 46598)
-- Name: tb_backups tb_backups_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tb_backups
    ADD CONSTRAINT tb_backups_pkey PRIMARY KEY (id);


--
-- TOC entry 5098 (class 2606 OID 45600)
-- Name: tb_blocos_rotina tb_blocos_rotina_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tb_blocos_rotina
    ADD CONSTRAINT tb_blocos_rotina_pkey PRIMARY KEY (id);


--
-- TOC entry 5246 (class 2606 OID 46584)
-- Name: tb_canais_notificacao tb_canais_notificacao_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tb_canais_notificacao
    ADD CONSTRAINT tb_canais_notificacao_pkey PRIMARY KEY (id);


--
-- TOC entry 5221 (class 2606 OID 46476)
-- Name: tb_compartilhamentos tb_compartilhamentos_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tb_compartilhamentos
    ADD CONSTRAINT tb_compartilhamentos_pkey PRIMARY KEY (id);


--
-- TOC entry 5223 (class 2606 OID 46478)
-- Name: tb_compartilhamentos tb_compartilhamentos_tipo_recurso_id_recurso_id_usuario_don_key; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tb_compartilhamentos
    ADD CONSTRAINT tb_compartilhamentos_tipo_recurso_id_recurso_id_usuario_don_key UNIQUE (tipo_recurso, id_recurso, id_usuario_dono, id_usuario_destino);


--
-- TOC entry 5233 (class 2606 OID 46532)
-- Name: tb_configuracoes tb_configuracoes_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tb_configuracoes
    ADD CONSTRAINT tb_configuracoes_pkey PRIMARY KEY (chave);


--
-- TOC entry 5184 (class 2606 OID 46357)
-- Name: tb_empresas tb_empresas_nome_key; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tb_empresas
    ADD CONSTRAINT tb_empresas_nome_key UNIQUE (nome);


--
-- TOC entry 5186 (class 2606 OID 46355)
-- Name: tb_empresas tb_empresas_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tb_empresas
    ADD CONSTRAINT tb_empresas_pkey PRIMARY KEY (id);


--
-- TOC entry 5129 (class 2606 OID 45746)
-- Name: tb_eventos_api tb_eventos_api_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tb_eventos_api
    ADD CONSTRAINT tb_eventos_api_pkey PRIMARY KEY (id);


--
-- TOC entry 5242 (class 2606 OID 46563)
-- Name: tb_fila_execucao tb_fila_execucao_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tb_fila_execucao
    ADD CONSTRAINT tb_fila_execucao_pkey PRIMARY KEY (id);


--
-- TOC entry 5103 (class 2606 OID 45614)
-- Name: tb_logs_execucao tb_logs_execucao_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tb_logs_execucao
    ADD CONSTRAINT tb_logs_execucao_pkey PRIMARY KEY (id);


--
-- TOC entry 5108 (class 2606 OID 45642)
-- Name: tb_logs_sistema tb_logs_sistema_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tb_logs_sistema
    ADD CONSTRAINT tb_logs_sistema_pkey PRIMARY KEY (id);


--
-- TOC entry 5110 (class 2606 OID 45656)
-- Name: tb_metricas_sistema tb_metricas_sistema_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tb_metricas_sistema
    ADD CONSTRAINT tb_metricas_sistema_pkey PRIMARY KEY (id);


--
-- TOC entry 5178 (class 2606 OID 46319)
-- Name: tb_notificacoes tb_notificacoes_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tb_notificacoes
    ADD CONSTRAINT tb_notificacoes_pkey PRIMARY KEY (id);


--
-- TOC entry 5254 (class 2606 OID 46610)
-- Name: tb_password_resets tb_password_resets_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tb_password_resets
    ADD CONSTRAINT tb_password_resets_pkey PRIMARY KEY (id);


--
-- TOC entry 5089 (class 2606 OID 45568)
-- Name: tb_perfis_conexao tb_perfis_conexao_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tb_perfis_conexao
    ADD CONSTRAINT tb_perfis_conexao_pkey PRIMARY KEY (id);


--
-- TOC entry 5173 (class 2606 OID 46293)
-- Name: tb_pipeline_execucoes tb_pipeline_execucoes_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tb_pipeline_execucoes
    ADD CONSTRAINT tb_pipeline_execucoes_pkey PRIMARY KEY (id);


--
-- TOC entry 5166 (class 2606 OID 46276)
-- Name: tb_pipelines tb_pipelines_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tb_pipelines
    ADD CONSTRAINT tb_pipelines_pkey PRIMARY KEY (id);


--
-- TOC entry 5190 (class 2606 OID 46377)
-- Name: tb_projetos tb_projetos_nome_id_empresa_key; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tb_projetos
    ADD CONSTRAINT tb_projetos_nome_id_empresa_key UNIQUE (nome, id_empresa);


--
-- TOC entry 5192 (class 2606 OID 46375)
-- Name: tb_projetos tb_projetos_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tb_projetos
    ADD CONSTRAINT tb_projetos_pkey PRIMARY KEY (id);


--
-- TOC entry 5181 (class 2606 OID 46332)
-- Name: tb_rate_limits tb_rate_limits_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tb_rate_limits
    ADD CONSTRAINT tb_rate_limits_pkey PRIMARY KEY (id);


--
-- TOC entry 5208 (class 2606 OID 46441)
-- Name: tb_recurso_empresas tb_recurso_empresas_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tb_recurso_empresas
    ADD CONSTRAINT tb_recurso_empresas_pkey PRIMARY KEY (id);


--
-- TOC entry 5210 (class 2606 OID 46443)
-- Name: tb_recurso_empresas tb_recurso_empresas_tipo_recurso_id_recurso_id_empresa_key; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tb_recurso_empresas
    ADD CONSTRAINT tb_recurso_empresas_tipo_recurso_id_recurso_id_empresa_key UNIQUE (tipo_recurso, id_recurso, id_empresa);


--
-- TOC entry 5214 (class 2606 OID 46458)
-- Name: tb_recurso_projetos tb_recurso_projetos_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tb_recurso_projetos
    ADD CONSTRAINT tb_recurso_projetos_pkey PRIMARY KEY (id);


--
-- TOC entry 5216 (class 2606 OID 46460)
-- Name: tb_recurso_projetos tb_recurso_projetos_tipo_recurso_id_recurso_id_projeto_key; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tb_recurso_projetos
    ADD CONSTRAINT tb_recurso_projetos_tipo_recurso_id_recurso_id_projeto_key UNIQUE (tipo_recurso, id_recurso, id_projeto);


--
-- TOC entry 5096 (class 2606 OID 45580)
-- Name: tb_rotinas tb_rotinas_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tb_rotinas
    ADD CONSTRAINT tb_rotinas_pkey PRIMARY KEY (id);


--
-- TOC entry 5196 (class 2606 OID 46399)
-- Name: tb_usuario_empresas tb_usuario_empresas_id_usuario_id_empresa_key; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tb_usuario_empresas
    ADD CONSTRAINT tb_usuario_empresas_id_usuario_id_empresa_key UNIQUE (id_usuario, id_empresa);


--
-- TOC entry 5198 (class 2606 OID 46397)
-- Name: tb_usuario_empresas tb_usuario_empresas_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tb_usuario_empresas
    ADD CONSTRAINT tb_usuario_empresas_pkey PRIMARY KEY (id);


--
-- TOC entry 5202 (class 2606 OID 46421)
-- Name: tb_usuario_projetos tb_usuario_projetos_id_usuario_id_projeto_key; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tb_usuario_projetos
    ADD CONSTRAINT tb_usuario_projetos_id_usuario_id_projeto_key UNIQUE (id_usuario, id_projeto);


--
-- TOC entry 5204 (class 2606 OID 46419)
-- Name: tb_usuario_projetos tb_usuario_projetos_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tb_usuario_projetos
    ADD CONSTRAINT tb_usuario_projetos_pkey PRIMARY KEY (id);


--
-- TOC entry 5087 (class 2606 OID 45559)
-- Name: tb_usuarios tb_usuarios_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tb_usuarios
    ADD CONSTRAINT tb_usuarios_pkey PRIMARY KEY (id);


--
-- TOC entry 5134 (class 2606 OID 45766)
-- Name: tb_valores_capturados tb_valores_capturados_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tb_valores_capturados
    ADD CONSTRAINT tb_valores_capturados_pkey PRIMARY KEY (id);


--
-- TOC entry 5235 (class 2606 OID 46547)
-- Name: tb_webhooks tb_webhooks_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tb_webhooks
    ADD CONSTRAINT tb_webhooks_pkey PRIMARY KEY (id);


--
-- TOC entry 5114 (class 2606 OID 45667)
-- Name: tb_worker_heartbeat tb_worker_heartbeat_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tb_worker_heartbeat
    ADD CONSTRAINT tb_worker_heartbeat_pkey PRIMARY KEY (id);


--
-- TOC entry 5116 (class 2606 OID 45669)
-- Name: tb_worker_heartbeat tb_worker_heartbeat_worker_id_key; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tb_worker_heartbeat
    ADD CONSTRAINT tb_worker_heartbeat_worker_id_key UNIQUE (worker_id);


--
-- TOC entry 5149 (class 2606 OID 45834)
-- Name: tb_workflow_edges tb_workflow_edges_id_workflow_edge_id_key; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tb_workflow_edges
    ADD CONSTRAINT tb_workflow_edges_id_workflow_edge_id_key UNIQUE (id_workflow, edge_id);


--
-- TOC entry 5151 (class 2606 OID 45832)
-- Name: tb_workflow_edges tb_workflow_edges_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tb_workflow_edges
    ADD CONSTRAINT tb_workflow_edges_pkey PRIMARY KEY (id);


--
-- TOC entry 5156 (class 2606 OID 45862)
-- Name: tb_workflow_execucoes tb_workflow_execucoes_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tb_workflow_execucoes
    ADD CONSTRAINT tb_workflow_execucoes_pkey PRIMARY KEY (id);


--
-- TOC entry 5161 (class 2606 OID 45884)
-- Name: tb_workflow_node_execucoes tb_workflow_node_execucoes_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tb_workflow_node_execucoes
    ADD CONSTRAINT tb_workflow_node_execucoes_pkey PRIMARY KEY (id);


--
-- TOC entry 5142 (class 2606 OID 45813)
-- Name: tb_workflow_nodes tb_workflow_nodes_id_workflow_node_id_key; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tb_workflow_nodes
    ADD CONSTRAINT tb_workflow_nodes_id_workflow_node_id_key UNIQUE (id_workflow, node_id);


--
-- TOC entry 5144 (class 2606 OID 45811)
-- Name: tb_workflow_nodes tb_workflow_nodes_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tb_workflow_nodes
    ADD CONSTRAINT tb_workflow_nodes_pkey PRIMARY KEY (id);


--
-- TOC entry 5138 (class 2606 OID 45795)
-- Name: tb_workflows tb_workflows_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tb_workflows
    ADD CONSTRAINT tb_workflows_pkey PRIMARY KEY (id);


--
-- TOC entry 5091 (class 2606 OID 45570)
-- Name: tb_perfis_conexao uq_tb_perfis_conexao_nome; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tb_perfis_conexao
    ADD CONSTRAINT uq_tb_perfis_conexao_nome UNIQUE (nome_conexao);


--
-- TOC entry 5121 (class 1259 OID 45729)
-- Name: idx_api_externas_ativo; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_api_externas_ativo ON public.tb_api_externas USING btree (ativo);


--
-- TOC entry 5122 (class 1259 OID 45730)
-- Name: idx_api_externas_nome; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_api_externas_nome ON public.tb_api_externas USING btree (nome);


--
-- TOC entry 5224 (class 1259 OID 46519)
-- Name: idx_auditoria_acao; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_auditoria_acao ON public.tb_auditoria USING btree (acao);


--
-- TOC entry 5225 (class 1259 OID 46522)
-- Name: idx_auditoria_criado_em; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_auditoria_criado_em ON public.tb_auditoria USING btree (criado_em);


--
-- TOC entry 5226 (class 1259 OID 46520)
-- Name: idx_auditoria_entidade; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_auditoria_entidade ON public.tb_auditoria USING btree (entidade);


--
-- TOC entry 5227 (class 1259 OID 46523)
-- Name: idx_auditoria_entidade_id; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_auditoria_entidade_id ON public.tb_auditoria USING btree (entidade, entidade_id);


--
-- TOC entry 5228 (class 1259 OID 46521)
-- Name: idx_auditoria_usuario; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_auditoria_usuario ON public.tb_auditoria USING btree (id_usuario);


--
-- TOC entry 5247 (class 1259 OID 46599)
-- Name: idx_backups_status; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_backups_status ON public.tb_backups USING btree (status);


--
-- TOC entry 5248 (class 1259 OID 46600)
-- Name: idx_backups_tipo; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_backups_tipo ON public.tb_backups USING btree (tipo);


--
-- TOC entry 5243 (class 1259 OID 46586)
-- Name: idx_canais_ativo; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_canais_ativo ON public.tb_canais_notificacao USING btree (ativo);


--
-- TOC entry 5244 (class 1259 OID 46585)
-- Name: idx_canais_tipo; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_canais_tipo ON public.tb_canais_notificacao USING btree (tipo);


--
-- TOC entry 5217 (class 1259 OID 46490)
-- Name: idx_comp_destino; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_comp_destino ON public.tb_compartilhamentos USING btree (id_usuario_destino);


--
-- TOC entry 5218 (class 1259 OID 46489)
-- Name: idx_comp_dono; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_comp_dono ON public.tb_compartilhamentos USING btree (id_usuario_dono);


--
-- TOC entry 5219 (class 1259 OID 46491)
-- Name: idx_comp_recurso; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_comp_recurso ON public.tb_compartilhamentos USING btree (tipo_recurso, id_recurso);


--
-- TOC entry 5231 (class 1259 OID 46533)
-- Name: idx_configuracoes_grupo; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_configuracoes_grupo ON public.tb_configuracoes USING btree (grupo);


--
-- TOC entry 5182 (class 1259 OID 46363)
-- Name: idx_empresas_ativa; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_empresas_ativa ON public.tb_empresas USING btree (ativa);


--
-- TOC entry 5125 (class 1259 OID 45753)
-- Name: idx_eventos_api_ativo; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_eventos_api_ativo ON public.tb_eventos_api USING btree (ativo);


--
-- TOC entry 5126 (class 1259 OID 45752)
-- Name: idx_eventos_api_id_api; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_eventos_api_id_api ON public.tb_eventos_api USING btree (id_api);


--
-- TOC entry 5127 (class 1259 OID 45754)
-- Name: idx_eventos_api_workflow; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_eventos_api_workflow ON public.tb_eventos_api USING btree (id_workflow);


--
-- TOC entry 5236 (class 1259 OID 46567)
-- Name: idx_fila_agendado; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_fila_agendado ON public.tb_fila_execucao USING btree (agendado_para) WHERE ((status)::text = 'pendente'::text);


--
-- TOC entry 5237 (class 1259 OID 46565)
-- Name: idx_fila_prioridade; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_fila_prioridade ON public.tb_fila_execucao USING btree (prioridade, criado_em);


--
-- TOC entry 5238 (class 1259 OID 46564)
-- Name: idx_fila_status; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_fila_status ON public.tb_fila_execucao USING btree (status);


--
-- TOC entry 5239 (class 1259 OID 46566)
-- Name: idx_fila_tipo; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_fila_tipo ON public.tb_fila_execucao USING btree (tipo, id_recurso);


--
-- TOC entry 5240 (class 1259 OID 46568)
-- Name: idx_fila_usuario; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_fila_usuario ON public.tb_fila_execucao USING btree (id_usuario);


--
-- TOC entry 5111 (class 1259 OID 45670)
-- Name: idx_heartbeat_status; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_heartbeat_status ON public.tb_worker_heartbeat USING btree (status);


--
-- TOC entry 5112 (class 1259 OID 45671)
-- Name: idx_heartbeat_ultimo; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_heartbeat_ultimo ON public.tb_worker_heartbeat USING btree (ultimo_heartbeat DESC);


--
-- TOC entry 5104 (class 1259 OID 45645)
-- Name: idx_logs_canal; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_logs_canal ON public.tb_logs_sistema USING btree (canal);


--
-- TOC entry 5117 (class 1259 OID 45692)
-- Name: idx_logs_categoria; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_logs_categoria ON public.logs_sistema USING btree (categoria);


--
-- TOC entry 5118 (class 1259 OID 45693)
-- Name: idx_logs_created_at; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_logs_created_at ON public.logs_sistema USING btree (created_at);


--
-- TOC entry 5105 (class 1259 OID 45644)
-- Name: idx_logs_criado_em; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_logs_criado_em ON public.tb_logs_sistema USING btree (criado_em DESC);


--
-- TOC entry 5099 (class 1259 OID 45629)
-- Name: idx_logs_data_inicio; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_logs_data_inicio ON public.tb_logs_execucao USING btree (data_inicio DESC);


--
-- TOC entry 5106 (class 1259 OID 45643)
-- Name: idx_logs_nivel; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_logs_nivel ON public.tb_logs_sistema USING btree (nivel);


--
-- TOC entry 5100 (class 1259 OID 45630)
-- Name: idx_logs_rotina_data; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_logs_rotina_data ON public.tb_logs_execucao USING btree (id_rotina, data_inicio DESC);


--
-- TOC entry 5101 (class 1259 OID 45628)
-- Name: idx_logs_status; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_logs_status ON public.tb_logs_execucao USING btree (status);


--
-- TOC entry 5157 (class 1259 OID 45892)
-- Name: idx_node_exec_node_id; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_node_exec_node_id ON public.tb_workflow_node_execucoes USING btree (node_id);


--
-- TOC entry 5158 (class 1259 OID 45891)
-- Name: idx_node_exec_status; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_node_exec_status ON public.tb_workflow_node_execucoes USING btree (status);


--
-- TOC entry 5159 (class 1259 OID 45890)
-- Name: idx_node_exec_workflow_exec; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_node_exec_workflow_exec ON public.tb_workflow_node_execucoes USING btree (id_workflow_execucao);


--
-- TOC entry 5174 (class 1259 OID 46322)
-- Name: idx_notificacoes_created; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_notificacoes_created ON public.tb_notificacoes USING btree (created_at DESC);


--
-- TOC entry 5175 (class 1259 OID 46320)
-- Name: idx_notificacoes_lida; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_notificacoes_lida ON public.tb_notificacoes USING btree (lida);


--
-- TOC entry 5176 (class 1259 OID 46321)
-- Name: idx_notificacoes_tipo; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_notificacoes_tipo ON public.tb_notificacoes USING btree (tipo);


--
-- TOC entry 5251 (class 1259 OID 46616)
-- Name: idx_password_resets_token; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_password_resets_token ON public.tb_password_resets USING btree (token_hash);


--
-- TOC entry 5252 (class 1259 OID 46617)
-- Name: idx_password_resets_usuario; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_password_resets_usuario ON public.tb_password_resets USING btree (id_usuario);


--
-- TOC entry 5167 (class 1259 OID 46299)
-- Name: idx_pipe_exec_pipeline; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_pipe_exec_pipeline ON public.tb_pipeline_execucoes USING btree (id_pipeline);


--
-- TOC entry 5168 (class 1259 OID 46300)
-- Name: idx_pipe_exec_status; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_pipe_exec_status ON public.tb_pipeline_execucoes USING btree (status);


--
-- TOC entry 5169 (class 1259 OID 46306)
-- Name: idx_pipeline_exec_data; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_pipeline_exec_data ON public.tb_pipeline_execucoes USING btree (data_inicio);


--
-- TOC entry 5170 (class 1259 OID 46304)
-- Name: idx_pipeline_exec_pipeline; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_pipeline_exec_pipeline ON public.tb_pipeline_execucoes USING btree (id_pipeline);


--
-- TOC entry 5171 (class 1259 OID 46305)
-- Name: idx_pipeline_exec_status; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_pipeline_exec_status ON public.tb_pipeline_execucoes USING btree (status);


--
-- TOC entry 5162 (class 1259 OID 46301)
-- Name: idx_pipelines_ativo; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_pipelines_ativo ON public.tb_pipelines USING btree (ativo);


--
-- TOC entry 5163 (class 1259 OID 46302)
-- Name: idx_pipelines_modo; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_pipelines_modo ON public.tb_pipelines USING btree (modo);


--
-- TOC entry 5164 (class 1259 OID 46303)
-- Name: idx_pipelines_trigger_tipo; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_pipelines_trigger_tipo ON public.tb_pipelines USING btree (trigger_tipo);


--
-- TOC entry 5187 (class 1259 OID 46389)
-- Name: idx_projetos_criador; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_projetos_criador ON public.tb_projetos USING btree (criado_por);


--
-- TOC entry 5188 (class 1259 OID 46388)
-- Name: idx_projetos_empresa; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_projetos_empresa ON public.tb_projetos USING btree (id_empresa);


--
-- TOC entry 5179 (class 1259 OID 46333)
-- Name: idx_rate_limits_chave; Type: INDEX; Schema: public; Owner: postgres
--

CREATE UNIQUE INDEX idx_rate_limits_chave ON public.tb_rate_limits USING btree (chave);


--
-- TOC entry 5205 (class 1259 OID 46450)
-- Name: idx_re_empresa; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_re_empresa ON public.tb_recurso_empresas USING btree (id_empresa);


--
-- TOC entry 5206 (class 1259 OID 46449)
-- Name: idx_re_tipo_recurso; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_re_tipo_recurso ON public.tb_recurso_empresas USING btree (tipo_recurso, id_recurso);


--
-- TOC entry 5092 (class 1259 OID 45627)
-- Name: idx_rotinas_ativa_proxima; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_rotinas_ativa_proxima ON public.tb_rotinas USING btree (ativa, proxima_execucao) WHERE (ativa = true);


--
-- TOC entry 5093 (class 1259 OID 45709)
-- Name: idx_rotinas_datas_ignorar; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_rotinas_datas_ignorar ON public.tb_rotinas USING gin (datas_ignorar_json);


--
-- TOC entry 5094 (class 1259 OID 45708)
-- Name: idx_rotinas_periodo_agendamento; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_rotinas_periodo_agendamento ON public.tb_rotinas USING btree (data_inicio, data_fim) WHERE (agendamento_cron IS NOT NULL);


--
-- TOC entry 5211 (class 1259 OID 46467)
-- Name: idx_rp_projeto; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_rp_projeto ON public.tb_recurso_projetos USING btree (id_projeto);


--
-- TOC entry 5212 (class 1259 OID 46466)
-- Name: idx_rp_tipo_recurso; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_rp_tipo_recurso ON public.tb_recurso_projetos USING btree (tipo_recurso, id_recurso);


--
-- TOC entry 5193 (class 1259 OID 46411)
-- Name: idx_ue_empresa; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_ue_empresa ON public.tb_usuario_empresas USING btree (id_empresa);


--
-- TOC entry 5194 (class 1259 OID 46410)
-- Name: idx_ue_usuario; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_ue_usuario ON public.tb_usuario_empresas USING btree (id_usuario);


--
-- TOC entry 5199 (class 1259 OID 46433)
-- Name: idx_up_projeto; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_up_projeto ON public.tb_usuario_projetos USING btree (id_projeto);


--
-- TOC entry 5200 (class 1259 OID 46432)
-- Name: idx_up_usuario; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_up_usuario ON public.tb_usuario_projetos USING btree (id_usuario);


--
-- TOC entry 5130 (class 1259 OID 45779)
-- Name: idx_valores_capturados_data; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_valores_capturados_data ON public.tb_valores_capturados USING btree (data_captura);


--
-- TOC entry 5131 (class 1259 OID 45777)
-- Name: idx_valores_capturados_evento; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_valores_capturados_evento ON public.tb_valores_capturados USING btree (id_evento);


--
-- TOC entry 5132 (class 1259 OID 45778)
-- Name: idx_valores_capturados_processado; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_valores_capturados_processado ON public.tb_valores_capturados USING btree (processado);


--
-- TOC entry 5145 (class 1259 OID 45842)
-- Name: idx_workflow_edges_destino; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_workflow_edges_destino ON public.tb_workflow_edges USING btree (node_destino);


--
-- TOC entry 5146 (class 1259 OID 45841)
-- Name: idx_workflow_edges_origem; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_workflow_edges_origem ON public.tb_workflow_edges USING btree (node_origem);


--
-- TOC entry 5147 (class 1259 OID 45840)
-- Name: idx_workflow_edges_workflow; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_workflow_edges_workflow ON public.tb_workflow_edges USING btree (id_workflow);


--
-- TOC entry 5152 (class 1259 OID 45870)
-- Name: idx_workflow_exec_data; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_workflow_exec_data ON public.tb_workflow_execucoes USING btree (data_inicio);


--
-- TOC entry 5153 (class 1259 OID 45869)
-- Name: idx_workflow_exec_status; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_workflow_exec_status ON public.tb_workflow_execucoes USING btree (status);


--
-- TOC entry 5154 (class 1259 OID 45868)
-- Name: idx_workflow_exec_workflow; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_workflow_exec_workflow ON public.tb_workflow_execucoes USING btree (id_workflow);


--
-- TOC entry 5139 (class 1259 OID 45820)
-- Name: idx_workflow_nodes_tipo; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_workflow_nodes_tipo ON public.tb_workflow_nodes USING btree (tipo_node);


--
-- TOC entry 5140 (class 1259 OID 45819)
-- Name: idx_workflow_nodes_workflow; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_workflow_nodes_workflow ON public.tb_workflow_nodes USING btree (id_workflow);


--
-- TOC entry 5135 (class 1259 OID 45796)
-- Name: idx_workflows_ativo; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_workflows_ativo ON public.tb_workflows USING btree (ativo);


--
-- TOC entry 5136 (class 1259 OID 45797)
-- Name: idx_workflows_trigger; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_workflows_trigger ON public.tb_workflows USING btree (trigger_tipo);


--
-- TOC entry 5262 (class 2606 OID 45687)
-- Name: logs_sistema fk_logs_usuario; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.logs_sistema
    ADD CONSTRAINT fk_logs_usuario FOREIGN KEY (usuario_id) REFERENCES public.tb_usuarios(id) ON DELETE SET NULL;


--
-- TOC entry 5256 (class 2606 OID 46502)
-- Name: schedules schedules_criado_por_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.schedules
    ADD CONSTRAINT schedules_criado_por_fkey FOREIGN KEY (criado_por) REFERENCES public.tb_usuarios(id) ON DELETE SET NULL;


--
-- TOC entry 5255 (class 2606 OID 45503)
-- Name: tb_auditoria_rotina tb_auditoria_rotina_id_arquivo_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tb_auditoria_rotina
    ADD CONSTRAINT tb_auditoria_rotina_id_arquivo_fkey FOREIGN KEY (id_arquivo) REFERENCES public.tb_arquivos(id);


--
-- TOC entry 5260 (class 2606 OID 45601)
-- Name: tb_blocos_rotina tb_blocos_rotina_id_rotina_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tb_blocos_rotina
    ADD CONSTRAINT tb_blocos_rotina_id_rotina_fkey FOREIGN KEY (id_rotina) REFERENCES public.tb_rotinas(id) ON DELETE CASCADE;


--
-- TOC entry 5281 (class 2606 OID 46484)
-- Name: tb_compartilhamentos tb_compartilhamentos_id_usuario_destino_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tb_compartilhamentos
    ADD CONSTRAINT tb_compartilhamentos_id_usuario_destino_fkey FOREIGN KEY (id_usuario_destino) REFERENCES public.tb_usuarios(id) ON DELETE CASCADE;


--
-- TOC entry 5282 (class 2606 OID 46479)
-- Name: tb_compartilhamentos tb_compartilhamentos_id_usuario_dono_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tb_compartilhamentos
    ADD CONSTRAINT tb_compartilhamentos_id_usuario_dono_fkey FOREIGN KEY (id_usuario_dono) REFERENCES public.tb_usuarios(id) ON DELETE CASCADE;


--
-- TOC entry 5272 (class 2606 OID 46358)
-- Name: tb_empresas tb_empresas_criado_por_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tb_empresas
    ADD CONSTRAINT tb_empresas_criado_por_fkey FOREIGN KEY (criado_por) REFERENCES public.tb_usuarios(id) ON DELETE SET NULL;


--
-- TOC entry 5263 (class 2606 OID 46497)
-- Name: tb_eventos_api tb_eventos_api_criado_por_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tb_eventos_api
    ADD CONSTRAINT tb_eventos_api_criado_por_fkey FOREIGN KEY (criado_por) REFERENCES public.tb_usuarios(id) ON DELETE SET NULL;


--
-- TOC entry 5264 (class 2606 OID 45747)
-- Name: tb_eventos_api tb_eventos_api_id_api_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tb_eventos_api
    ADD CONSTRAINT tb_eventos_api_id_api_fkey FOREIGN KEY (id_api) REFERENCES public.tb_api_externas(id) ON DELETE CASCADE;


--
-- TOC entry 5261 (class 2606 OID 45615)
-- Name: tb_logs_execucao tb_logs_execucao_id_rotina_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tb_logs_execucao
    ADD CONSTRAINT tb_logs_execucao_id_rotina_fkey FOREIGN KEY (id_rotina) REFERENCES public.tb_rotinas(id) ON DELETE SET NULL;


--
-- TOC entry 5283 (class 2606 OID 46611)
-- Name: tb_password_resets tb_password_resets_id_usuario_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tb_password_resets
    ADD CONSTRAINT tb_password_resets_id_usuario_fkey FOREIGN KEY (id_usuario) REFERENCES public.tb_usuarios(id) ON DELETE CASCADE;


--
-- TOC entry 5257 (class 2606 OID 46492)
-- Name: tb_perfis_conexao tb_perfis_conexao_criado_por_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tb_perfis_conexao
    ADD CONSTRAINT tb_perfis_conexao_criado_por_fkey FOREIGN KEY (criado_por) REFERENCES public.tb_usuarios(id) ON DELETE SET NULL;


--
-- TOC entry 5271 (class 2606 OID 46294)
-- Name: tb_pipeline_execucoes tb_pipeline_execucoes_id_pipeline_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tb_pipeline_execucoes
    ADD CONSTRAINT tb_pipeline_execucoes_id_pipeline_fkey FOREIGN KEY (id_pipeline) REFERENCES public.tb_pipelines(id) ON DELETE CASCADE;


--
-- TOC entry 5273 (class 2606 OID 46383)
-- Name: tb_projetos tb_projetos_criado_por_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tb_projetos
    ADD CONSTRAINT tb_projetos_criado_por_fkey FOREIGN KEY (criado_por) REFERENCES public.tb_usuarios(id) ON DELETE SET NULL;


--
-- TOC entry 5274 (class 2606 OID 46378)
-- Name: tb_projetos tb_projetos_id_empresa_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tb_projetos
    ADD CONSTRAINT tb_projetos_id_empresa_fkey FOREIGN KEY (id_empresa) REFERENCES public.tb_empresas(id) ON DELETE CASCADE;


--
-- TOC entry 5279 (class 2606 OID 46444)
-- Name: tb_recurso_empresas tb_recurso_empresas_id_empresa_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tb_recurso_empresas
    ADD CONSTRAINT tb_recurso_empresas_id_empresa_fkey FOREIGN KEY (id_empresa) REFERENCES public.tb_empresas(id) ON DELETE CASCADE;


--
-- TOC entry 5280 (class 2606 OID 46461)
-- Name: tb_recurso_projetos tb_recurso_projetos_id_projeto_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tb_recurso_projetos
    ADD CONSTRAINT tb_recurso_projetos_id_projeto_fkey FOREIGN KEY (id_projeto) REFERENCES public.tb_projetos(id) ON DELETE CASCADE;


--
-- TOC entry 5258 (class 2606 OID 45581)
-- Name: tb_rotinas tb_rotinas_id_conexao_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tb_rotinas
    ADD CONSTRAINT tb_rotinas_id_conexao_fkey FOREIGN KEY (id_conexao) REFERENCES public.tb_perfis_conexao(id) ON DELETE RESTRICT;


--
-- TOC entry 5259 (class 2606 OID 45586)
-- Name: tb_rotinas tb_rotinas_id_usuario_criador_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tb_rotinas
    ADD CONSTRAINT tb_rotinas_id_usuario_criador_fkey FOREIGN KEY (id_usuario_criador) REFERENCES public.tb_usuarios(id) ON DELETE SET NULL;


--
-- TOC entry 5275 (class 2606 OID 46405)
-- Name: tb_usuario_empresas tb_usuario_empresas_id_empresa_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tb_usuario_empresas
    ADD CONSTRAINT tb_usuario_empresas_id_empresa_fkey FOREIGN KEY (id_empresa) REFERENCES public.tb_empresas(id) ON DELETE CASCADE;


--
-- TOC entry 5276 (class 2606 OID 46400)
-- Name: tb_usuario_empresas tb_usuario_empresas_id_usuario_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tb_usuario_empresas
    ADD CONSTRAINT tb_usuario_empresas_id_usuario_fkey FOREIGN KEY (id_usuario) REFERENCES public.tb_usuarios(id) ON DELETE CASCADE;


--
-- TOC entry 5277 (class 2606 OID 46427)
-- Name: tb_usuario_projetos tb_usuario_projetos_id_projeto_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tb_usuario_projetos
    ADD CONSTRAINT tb_usuario_projetos_id_projeto_fkey FOREIGN KEY (id_projeto) REFERENCES public.tb_projetos(id) ON DELETE CASCADE;


--
-- TOC entry 5278 (class 2606 OID 46422)
-- Name: tb_usuario_projetos tb_usuario_projetos_id_usuario_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tb_usuario_projetos
    ADD CONSTRAINT tb_usuario_projetos_id_usuario_fkey FOREIGN KEY (id_usuario) REFERENCES public.tb_usuarios(id) ON DELETE CASCADE;


--
-- TOC entry 5265 (class 2606 OID 45772)
-- Name: tb_valores_capturados tb_valores_capturados_id_api_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tb_valores_capturados
    ADD CONSTRAINT tb_valores_capturados_id_api_fkey FOREIGN KEY (id_api) REFERENCES public.tb_api_externas(id) ON DELETE CASCADE;


--
-- TOC entry 5266 (class 2606 OID 45767)
-- Name: tb_valores_capturados tb_valores_capturados_id_evento_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tb_valores_capturados
    ADD CONSTRAINT tb_valores_capturados_id_evento_fkey FOREIGN KEY (id_evento) REFERENCES public.tb_eventos_api(id) ON DELETE CASCADE;


--
-- TOC entry 5268 (class 2606 OID 45835)
-- Name: tb_workflow_edges tb_workflow_edges_id_workflow_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tb_workflow_edges
    ADD CONSTRAINT tb_workflow_edges_id_workflow_fkey FOREIGN KEY (id_workflow) REFERENCES public.tb_workflows(id) ON DELETE CASCADE;


--
-- TOC entry 5269 (class 2606 OID 45863)
-- Name: tb_workflow_execucoes tb_workflow_execucoes_id_workflow_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tb_workflow_execucoes
    ADD CONSTRAINT tb_workflow_execucoes_id_workflow_fkey FOREIGN KEY (id_workflow) REFERENCES public.tb_workflows(id) ON DELETE CASCADE;


--
-- TOC entry 5270 (class 2606 OID 45885)
-- Name: tb_workflow_node_execucoes tb_workflow_node_execucoes_id_workflow_execucao_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tb_workflow_node_execucoes
    ADD CONSTRAINT tb_workflow_node_execucoes_id_workflow_execucao_fkey FOREIGN KEY (id_workflow_execucao) REFERENCES public.tb_workflow_execucoes(id) ON DELETE CASCADE;


--
-- TOC entry 5267 (class 2606 OID 45814)
-- Name: tb_workflow_nodes tb_workflow_nodes_id_workflow_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tb_workflow_nodes
    ADD CONSTRAINT tb_workflow_nodes_id_workflow_fkey FOREIGN KEY (id_workflow) REFERENCES public.tb_workflows(id) ON DELETE CASCADE;


-- Completed on 2026-03-20 17:57:13

--
-- PostgreSQL database dump complete
--

