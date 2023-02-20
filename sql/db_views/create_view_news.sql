DROP VIEW IF EXISTS view_news;

CREATE VIEW `view_news` AS
    SELECT 
        year as Id,
        year as year,
        news_date as news_date
        severity as severity,
        (CASE 
            WHEN severity= 0 THEN 'Meddelande'
            WHEN severity= 1 THEN 'Viktigt meddelande'
            WHEN severity= 2 THEN 'Varning'
            ELSE '-'
            END
        ) AS severityText,
        writer as writer        
    FROM
        news