DROP FUNCTION psd_analytic(text,text,text,text,json) 
CREATE OR REPLACE FUNCTION psd_analytic(
    tname TEXT,
    row_id TEXT,
    col_id TEXT,
    val TEXT,
    filters JSON DEFAULT NULL
)
RETURNS json LANGUAGE plpgsql AS $$
DECLARE
    labels TEXT;
    col_type TEXT;
    query TEXT;
    col_query TEXT;
    main_query TEXT;
   build_json text;
   	result json;
BEGIN
    -- Check if column exists and get its type
    SELECT c.data_type INTO col_type
    FROM information_schema.columns c
    WHERE c.table_name = tname AND c.column_name = col_id;

    IF col_type IS NULL THEN
        RAISE EXCEPTION 'Column % does not exist in table %', col_id, tname;
    END IF;

    -- Get distinct column labels for crosstab
    EXECUTE FORMAT(
        'SELECT string_agg(DISTINCT ''"'' || "%I" || ''"'' || '' %I'', '', '') FROM %I',
        col_id, col_type, tname
    ) INTO labels;
   


    IF labels IS NULL THEN
        RAISE EXCEPTION 'Failed to generate column labels for crosstab';
    END IF;

    -- Generate crosstab query dynamically
    col_query := FORMAT(
        'SELECT DISTINCT %I FROM %I ORDER BY %I',
        col_id, tname, col_id
    );

    main_query := FORMAT(
        'SELECT * FROM crosstab(
            ''SELECT %I, %I, COUNT(%I) FROM %I GROUP BY 1, 2 ORDER BY 1, 2'',
            ''%s''
        ) AS ct(%I TEXT, %s)',
        row_id, col_id, val, tname, col_query, row_id, labels
    );
   
      RAISE NOTICE 'log val %', main_query;
     
        build_json := FORMAT(
        'SELECT string_agg FROM %s',
        row_id, col_id, val, tname, col_query, row_id, labels
    );
   
      RAISE NOTICE 'log val %', main_query;

    EXECUTE main_query into result;
   	return json_build_object();
END;
$$;

