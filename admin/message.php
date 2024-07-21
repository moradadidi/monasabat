<?php
include_once("dashbord.php");
include_once("../includes/database.php");

if (!isset($_SESSION['id_user'])) {
    header("Location: login.php");
    exit();
}

// Handle the "Seen" button click to delete the message
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id_contact'])) {
    $id_contact = $_POST['id_contact'];
    try {
        $stmt = $pdo->prepare("DELETE FROM user_contacts WHERE id_contact = :id_contact");
        $stmt->bindParam(':id_contact', $id_contact, PDO::PARAM_INT);
        $stmt->execute();
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}

// Fetch all messages from the user_contacts table
try {
    $stmt = $pdo->prepare("SELECT * FROM user_contacts JOIN users ON user_contacts.id_user = users.id_user");
    $stmt->execute();
    $contacts = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>

<section class="home" style="background-color:white">
    <div class="container">
        <h2>Contact Messages</h2>
        <div class="table-container mt-12 shadow-sm border rounded-lg overflow-x-auto">
            <table class="w-full table-auto text-sm text-left">
                <thead class="bg-gray-50 text-gray-600 font-medium border-b">
                    <tr class="text-center">
                        <th class="py-3 px-6 text-left">User</th>
                        <th class="py-3 px-6 text-left">Subject</th>
                        <th class="py-3 px-6 text-left">Message</th>
                        <th class="py-3 px-6 text-left">Created At</th>
                        <th class="py-3 px-6 text-left">Seen</th>

                    </tr>
                </thead>
                <tbody class="text-gray-600 divide-y">
                <?php foreach ($contacts as $contact): ?>
                        <tr class="border-b hover:bg-gray-100">
                            <td class="py-3 px-6">
                            <div class="flex items-center">
                                 <div class="flex-shrink-0 h-10 w-10">
                                            <img class="h-10 w-10 rounded-full" src="../public/pictures/<?= htmlspecialchars($contact['photo']) ?>" alt="">
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900"><?= htmlspecialchars($contact['username']) ?></div>
                                            <div class="text-sm text-gray-500"><?= htmlspecialchars($contact['email']) ?></div>
                                        </div>
                                    </div></td>
                            <td class="py-3 px-6"><?php echo htmlspecialchars($contact['subject']); ?></td>
                            <td class="py-3 px-6"><?php echo htmlspecialchars($contact['message']); ?></td>
                            <td class="py-3 px-6"><?php echo htmlspecialchars($contact['created_at']); ?></td>
                            <td class="py-3 px-6">
                                <form action="" method="POST">
                                    <input type="hidden" name="id_contact" value="<?php echo htmlspecialchars($contact['id_contact']); ?>">
                                    <button type="submit" class="py-2 px-4 text-2xl text-green-500  rounded hover:text-green-700"><i class="fa-solid fa-check"></i></button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</section>
</body>
</html>
