CREATE OR REPLACE FUNCTION public.psd_analytic(tname text, row_id text, col_id text, val text, aggr text, chart_id text DEFAULT NULL::text, filters json DEFAULT NULL::json, exact_filters json DEFAULT NULL::json)
 RETURNS TABLE(result json)
 LANGUAGE plpgsql
AS $function$
DECLARE
    row_count INT;
    row_start INT := 1;
    batch_result JSONB;
    merged_json JSONB := '[]';
    main_query TEXT;
    filter_query TEXT := '';
    key TEXT;
    values TEXT;
    filter_condition TEXT;
BEGIN
    -- count total unique rows
    EXECUTE FORMAT('SELECT COUNT(DISTINCT %I) FROM %I', row_id, tname) INTO row_count;
   
   IF filters IS NOT NULL THEN
        FOR key, values IN 
            SELECT * FROM json_each_text(filters) 
        LOOP

            filter_condition := FORMAT('%I::text IN (%s)', key, 
                (SELECT string_agg(quote_literal(value), ', ') FROM json_array_elements_text(filters->key))
            );


            IF filter_query <> '' THEN
                filter_query := filter_query || ' AND ';
            END IF;
            filter_query := filter_query || filter_condition;
        END LOOP;
    END IF;
   
    IF exact_filters IS NOT NULL THEN
        FOR key, values IN 
            SELECT * FROM json_each_text(exact_filters) 
        LOOP

            filter_condition := FORMAT('%I::text NOT IN (%s)', key, 
                (SELECT string_agg(quote_literal(value), ', ') FROM json_array_elements_text(exact_filters->key))
            );


            IF filter_query <> '' THEN
                filter_query := filter_query || ' AND ';
            END IF;
            filter_query := filter_query || filter_condition;
        END LOOP;
    END IF;

    IF filter_query <> '' THEN
   		filter_query := format('WHERE %I is not null and ', col_id) || '(' || filter_query || ')';
   	else
   		filter_query := format('WHERE %I is not null', col_id);
    END IF;
   
   raise notice 'filter %s', filter_query;

    -- batches of 1600 rows
    WHILE row_start <= row_count LOOP
        -- aggregate JSON per row
        main_query := FORMAT(
            'WITH row_data AS (
                SELECT roww AS row_id, jsonb_object_agg(%I, result.val) AS data, COALESCE(''%s'', ''result'') as chart_id
                FROM (
                    SELECT %I as roww, %I, %I(%I) AS val
                    FROM %I
                    %s
                    GROUP BY %I, %I
                ) AS result
                GROUP BY roww
                ORDER BY roww
                LIMIT 1600 OFFSET %s
            )
            SELECT jsonb_agg(row_data) FROM row_data',
            col_id, chart_id, row_id, col_id, aggr, val, tname, filter_query, row_id, col_id, row_start - 1
        );
       
		RAISE NOTICE 'Query: %', main_query;

        EXECUTE main_query INTO batch_result;


        merged_json := merged_json || COALESCE(batch_result, '[]'::JSONB);

        row_start := row_start + 1600;
    END LOOP;

    -- final JSON
    RETURN QUERY SELECT merged_json::JSON;
END;
$function$
;
