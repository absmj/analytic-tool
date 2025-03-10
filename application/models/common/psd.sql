CREATE OR REPLACE FUNCTION public.psd_analytic(
    tname text, row_id text, col_id text, val text, aggr text, filters json DEFAULT NULL::json
)
RETURNS TABLE(result JSON)
LANGUAGE plpgsql
AS $function$
DECLARE
    labels TEXT;
    col_type TEXT;
    col_query TEXT;
    main_query TEXT;
    filter_query TEXT := '';
    key TEXT;
    values TEXT;
    filter_condition TEXT;
BEGIN

    SELECT 
        CASE 
            WHEN c.data_type IN ('integer', 'bigint', 'smallint', 'numeric', 'double precision') THEN 'numeric'
            ELSE c.data_type 
        END INTO col_type
    FROM information_schema.columns c
    WHERE c.table_name = tname AND c.column_name = col_id;

    IF col_type IS NULL THEN
        RAISE EXCEPTION 'Column % does not exist in table %', col_id, tname;
    END IF;

    IF col_type NOT IN ('integer', 'text', 'varchar', 'bigint', 'numeric', 'double precision') THEN
        RAISE EXCEPTION 'Unsupported column type for crosstab: %', col_type;
    END IF;

    IF filters IS NOT NULL THEN
        FOR key, values IN 
            SELECT * FROM json_each_text(filters) 
        LOOP
            -- Convert values into a comma-separated list
            filter_condition := FORMAT('%I IN (%s)', key, 
                (SELECT string_agg(quote_literal(value), ', ') FROM json_array_elements_text(filters->key))
            );

            -- Append condition to filter query
            IF filter_query <> '' THEN
                filter_query := filter_query || ' AND ';
            END IF;
            filter_query := filter_query || filter_condition;
        END LOOP;
    END IF;

    IF filter_query <> '' THEN
        filter_query := 'WHERE ' || filter_query;
    END IF;


    EXECUTE FORMAT(
        'SELECT string_agg(DISTINCT ''"'' || %I || ''" TEXT'', '', '') FROM %I %s',
        col_id, tname, filter_query
    ) INTO labels;

    IF labels IS NULL THEN
        RAISE EXCEPTION 'Failed to generate column labels for crosstab';
    END IF;


    col_query := FORMAT(
        'SELECT DISTINCT %I FROM %I %s ORDER BY %I',
        col_id, tname, filter_query, col_id
    );

	main_query := FORMAT(
	    'SELECT json_agg(result) FROM (
	        SELECT * FROM crosstab(
	            %L,  -- Properly quoted SQL query
	            %L   -- Properly quoted column query
	        ) AS ct(%I TEXT, %s)
	    ) AS result',
	    FORMAT(
	        'SELECT %I, %I, %I(%I) FROM %I %s GROUP BY 1, 2 ORDER BY 1, 2',
	        row_id, col_id, aggr, val, tname, filter_query
	    ),
	    col_query,
	    row_id, labels
	);

    RAISE NOTICE 'Query: %', main_query;

    RETURN QUERY EXECUTE main_query;

END;
$function$;
