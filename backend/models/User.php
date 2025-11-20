<?php
/**
 * Alias stub for the shared User model.
 * Prevents duplicate class definition errors and keeps backend compatibility.
 */

namespace backend\models;

// Create a namespaced alias so all existing `use backend\models\User` references
// still work and point to the main common\models\User class.
class_alias(\common\models\User::class, __NAMESPACE__ . '\User');
