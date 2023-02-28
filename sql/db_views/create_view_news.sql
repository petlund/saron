DROP VIEW IF EXISTS view_news;

CREATE VIEW `view_news` AS
    SELECT 
        Id as Id,
        news_date as news_date,
        severity as severity,
        information as information,
        (CASE 
            WHEN severity= 0 THEN 'Meddelande'
            WHEN severity= 1 THEN 'Viktigt meddelande'
            WHEN severity= 2 THEN 'Varning'
            ELSE '-'
            END
        ) AS severityText,
        writer as writer        
    FROM
        News