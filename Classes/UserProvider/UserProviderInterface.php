<?php
namespace GeorgRinger\GoogleSignin\UserProvider;

interface UserProviderInterface
{
    /**
     * Get a user by its uid.
     *
     * @param int $uid
     * @param bool $respectEnableFields
     * @return array
     */
    public function getUserById(int $uid, bool $respectEnableFields = true): array;

    /**
     * Get a user by its email address.
     *
     * @param string $email
     * @param bool $respectEnableFields
     * @return array
     */
    public function getUserByEmail(string $email, bool $respectEnableFields = true): array;

    /**
     * Creates a new user based on a configured skeleton user.
     *
     * @param string $email
     * @param string $name
     * @return void
     */
    public function copyUserFromSkeleton(string $email, string $name): void;

    /**
     * Check if the organisation from the google user that tries to login
     * matches one of the configured organisations.
     *
     * @param string $organisation
     * @return bool
     */
    public function isUserInApprovedOrganisation(string $organisation): bool;
}
