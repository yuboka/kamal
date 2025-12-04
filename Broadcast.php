<?php

// -------------------------
// CONFIGURATION
// -------------------------
$telegramBotToken = "7470800762:AAHEpWfdIoVPZLdH1GTdGjZV0q-Q_0Wa-Mk";
$telegramChatId   = "739928471";

// Viral news RSS feeds (choose any)
$feeds = [
    "https://news.google.com/rss?hl=en-NG&gl=NG&ceid=NG:en",   // Google News Nigeria
    "https://rss.cnn.com/rss/edition.rss",                    // CNN Top Stories
    "https://feeds.bbci.co.uk/news/rss.xml"                   // BBC News
];

// -------------------------
// FUNCTION: Fetch viral headlines
// -------------------------
function getViralHeadlines($feeds) {
    $headlines = [];

    foreach ($feeds as $feed) {
        $xml = @simplexml_load_file($feed);

        if (!$xml) continue;

        foreach ($xml->channel->item as $item) {
            $title = (string)$item->title;
            $link  = (string)$item->link;

            // Avoid long messages
            $headlines[] = "🔥 *$title* \n$link";
        }
    }

    return array_slice($headlines, 0, 5); // Return only top 5 viral headlines
}

// -------------------------
// FUNCTION: Send message to Telegram
// -------------------------
function sendToTelegram($botToken, $chatId, $message) {
    $url = "https://api.telegram.org/bot$botToken/sendMessage";

    $data = [
        'chat_id'    => $chatId,
        'text'       => $message,
        'parse_mode' => 'Markdown'
    ];

    file_get_contents($url . "?" . http_build_query($data));
}

// -------------------------
// MAIN PROCESS
// -------------------------
$viral = getViralHeadlines($feeds);

if (empty($viral)) {
    sendToTelegram($telegramBotToken, $telegramChatId, "⚠️ No viral news found today.");
    exit;
}

// Send each headline
foreach ($viral as $news) {
    sendToTelegram($telegramBotToken, $telegramChatId, $news);
}

echo "Broadcast sent successfully!";
