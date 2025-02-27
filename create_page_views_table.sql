-- Table to store aggregated page view data; create this table first --
CREATE TABLE page_views (
    page_url VARCHAR(255) NOT NULL PRIMARY KEY, -- unique url
    view_count INT DEFAULT 0, -- total view count
    unique_ip_count INT DEFAULT 0, -- unique ip count
    unique_user_agent_count INT DEFAULT 0, -- browser, device, OS, engine, etc.
    unique_referrer_count INT DEFAULT 0, -- HTTP Referer
    last_view TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);


-- Table to store unique IP addresses per page --
CREATE TABLE page_view_ips (
    page_url VARCHAR(255) NOT NULL, -- page url
    ip_address VARCHAR(45) NOT NULL, -- unique IP address
    view_count INT DEFAULT 1, -- number of views from an IP
    PRIMARY KEY (page_url, ip_address), -- ensures each IP is only stored once per page
    FOREIGN KEY (page_url) REFERENCES page_views(page_url) ON DELETE CASCADE -- deletes IP records if the page is removed
);


-- Table to store unique User-Agent views with a count --
CREATE TABLE page_view_user_agents (
    page_url VARCHAR(255) NOT NULL, -- Page URL
    user_agent VARCHAR(255) NOT NULL, -- Unique User-Agent string
    view_count INT DEFAULT 1, -- Number of views from this User-Agent
    PRIMARY KEY (page_url, user_agent), -- Prevents duplicate entries per page
    FOREIGN KEY (page_url) REFERENCES page_views(page_url) ON DELETE CASCADE -- Cascades deletion
);

-- Table to store unique Referrer views with a count --
CREATE TABLE page_view_referrers (
    page_url VARCHAR(255) NOT NULL, -- Page URL
    referrer VARCHAR(255) NOT NULL, -- Unique Referrer URL
    view_count INT DEFAULT 1, -- Number of views from this Referrer
    PRIMARY KEY (page_url, referrer), -- Prevents duplicate entries per page
    FOREIGN KEY (page_url) REFERENCES page_views(page_url) ON DELETE CASCADE -- Cascades deletion
);
