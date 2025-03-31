<?php

class User_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->ldap = ldap_connect(LDAP_SERVER) or die("Could not connect to LDAP server");
        ldap_set_option($this->ldap, LDAP_OPT_PROTOCOL_VERSION, 3);
        ldap_set_option($this->ldap, LDAP_OPT_REFERRALS, 0);
    }

    public function login($username, $password)
    {
        $dn = "uid={$username}," . LDAP_DN;

        if (ldap_bind($this->ldap, $dn, $password)) {
            $search_filter = "(uid=$username)";
            $_SESSION['username'] = $username;
            $attributes = [
                "cn",
                "givenName",
                "sn",
                "mail",
                "telephoneNumber",
                "uidnumber"
            ];

            $search = ldap_search($this->ldap, $dn, $search_filter, $attributes);

            if (!$search) {
                die("LDAP search failed: " . ldap_error($this->ldap));
            }

            $entries = ldap_get_entries($this->ldap, $search);

            if ($entries["count"] > 0) {
                $user = $entries[0]; // Get the first user entry
                $_SESSION['fullName'] = $user["cn"][0] ?? "N/A";
                $_SESSION['firstname'] =  $user["givenname"][0] ?? "N/A";
                $_SESSION['surname'] =  $user["sn"][0] ?? "N/A";
                $_SESSION['email'] =  $user["mail"][0] ?? "N/A";
            }

            $_SESSION['groups'] = $this->getGroups($username);
            $this->session->set_flashdata('success', 'Login successful.');
            return true;
        } else {
            $this->session->set_flashdata('error', 'Invalid username or password.');
            return false;
        }
    }

    public function getGroups($username = null)
    {
        $search_base = LDAP_DN;
        $attributes = [
            "displayName",
            "name",
            "cn",
            "ou",
            "o",
            "objectClass",
            "member",
            "memberUid",
            "uniqueMember"
        ];

        if ($username)
            $search_filter = "(|(memberUid={$username})(uniqueMember=uid={$username},dc=example,dc=com)(member=uid={$username},dc=example,dc=com))";
        else
            $search_filter = "(|(|(|(objectClass=posixGroup)(objectClass=groupOfUniqueNames))(objectClass=groupOfNames))(objectClass=group))";

        $search = ldap_search($this->ldap, $search_base, $search_filter, $attributes);

        if (!$search) {
            die("LDAP search failed: " . ldap_error($this->ldap));
        }

        $entries = ldap_get_entries($this->ldap, $search);
        $groups = [];
        // Display results
        if ($entries["count"] > 0) {
            foreach ($entries as $entry) {
                if (isset($entry["cn"][0])) {
                    $groups[] = $entry["cn"][0];
                }
            }
        }
        return $groups;
    }

    public function __destruct()
    {
        ldap_close($this->ldap);
    }
}
