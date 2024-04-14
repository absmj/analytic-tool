create table if not exists public.global_variable
(
    id              integer   default nextval('global_variables_id_seq'::regclass) not null
        constraint global_variables_pkey
            primary key,
    variable_name   varchar(255)                                                   not null,
    parameter_name  varchar(50)                                                    not null,
    parameter_value varchar(255)                                                   not null,
    created_at      timestamp default CURRENT_TIMESTAMP,
    updated_at      timestamp default CURRENT_TIMESTAMP,
    is_deleted      boolean   default false
);

alter table public.global_variable
    owner to postgres;

create table if not exists public.folders
(
    folder_id        serial
        primary key,
    folder_name      varchar(255) not null,
    parent_folder_id integer
        references public.folders,
    created_at       timestamp default CURRENT_TIMESTAMP
);

alter table public.folders
    owner to postgres;

create table if not exists public.crons
(
    id    serial
        primary key,
    job   varchar(255) not null
        unique,
    title varchar(255)
);

alter table public.crons
    owner to postgres;

create table if not exists public.queries
(
    id         serial
        primary key,
    sql        text        not null,
    cron_id    integer
        references public.crons,
    is_deleted boolean                  default false,
    created_at timestamp with time zone default CURRENT_TIMESTAMP,
    db         varchar(50) not null
);

comment on column public.queries.db is 'SQL-in işləyəcəyi baza';

alter table public.queries
    owner to postgres;

create table if not exists public.reports
(
    id         serial
        primary key,
    name       varchar(255) not null,
    query_id   integer
        references public.queries,
    folder_id  integer
        references public.folders,
    is_deleted boolean                  default false,
    created_at timestamp with time zone default CURRENT_TIMESTAMP,
    base       integer
);

comment on column public.reports.base is 'Report history id, when is active 0';

alter table public.reports
    owner to postgres;

create table if not exists public.files
(
    id         serial
        primary key,
    name       varchar(255) not null,
    location   text         not null,
    created_at timestamp with time zone default CURRENT_TIMESTAMP,
    is_deleted boolean                  default false,
    folder_id  integer,
    type       varchar(10)
);

alter table public.files
    owner to postgres;

create table if not exists public.accesses
(
    id      serial
        primary key,
    ldap    varchar(10),
    page_id integer
);

alter table public.accesses
    owner to postgres;

create table if not exists public.pages
(
    id         serial
        primary key,
    title      varchar(255),
    access     text,
    created_at date default CURRENT_DATE,
    updated_at date,
    template   varchar(50),
    report_id  integer
        constraint pages_report_id_fk
            references public.reports,
    run_method integer
);

alter table public.pages
    owner to postgres;

create table if not exists public.charts
(
    id         serial
        primary key,
    row_class  varchar(512),
    row_index  integer,
    col_class  varchar(255),
    chart_type varchar(25),
    title      varchar(255),
    slice      json,
    page_id    integer
        references public.pages,
    chart_id   varchar(100)
);

alter table public.charts
    owner to postgres;

create unique index if not exists charts_chart_id_uindex
    on public.charts (chart_id);

create table if not exists public.jobs
(
    id        serial
        primary key,
    report_id integer
        references public.reports,
    query_id  integer
        references public.queries,
    file_id   integer
        references public.files,
    is_cron   boolean   default false,
    date      timestamp default CURRENT_TIMESTAMP
);

alter table public.jobs
    owner to postgres;

